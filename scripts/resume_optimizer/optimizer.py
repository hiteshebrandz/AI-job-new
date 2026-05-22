#!/usr/bin/env python3
"""
Resume Optimizer CLI
Usage:
  python3 optimizer.py analyze <path_to_resume>
  python3 optimizer.py generate <path_to_resume> <analysis_json_path> <output_pdf_path>
"""

import json
import os
import sys

try:
    from dotenv import load_dotenv
    _env_path = os.path.join(os.path.dirname(__file__), '..', '..', '.env')
    load_dotenv(dotenv_path=os.path.abspath(_env_path), override=False)
except ImportError:
    pass

from ai_client import error, validate_provider_keys
from analyzer import analyze_resume
from generator import generate_optimized_resume
from text_extractor import extract_resume_text


def cmd_analyze(file_path: str) -> None:
    validate_provider_keys()

    if not os.path.isfile(file_path):
        error(f"File not found: {file_path}")

    ext = os.path.splitext(file_path)[1].lower()
    if ext not in ('.pdf', '.docx', '.doc'):
        error(f"Unsupported file type '{ext}'. Only .pdf, .doc, and .docx are accepted.")

    try:
        extracted_text = extract_resume_text(file_path)
    except Exception as exc:
        error(f"Text extraction failed: {exc}")

    try:
        data = analyze_resume(extracted_text)
    except SystemExit:
        raise
    except Exception as exc:
        error(f"AI analysis failed: {exc}")

    print(json.dumps({
        "success": True,
        "extracted_text": extracted_text,
        "data": data,
    }))


def _export_pdf(content: dict, output_path: str) -> None:
    os.makedirs(os.path.dirname(os.path.abspath(output_path)) or '.', exist_ok=True)
    try:
        from pdf_export import export_resume_to_pdf
        export_resume_to_pdf(content, output_path)
    except ImportError:
        error(
            "PDF export requires reportlab. Install with: "
            "pip install -r scripts/resume_optimizer/requirements.txt"
        )
    except Exception as exc:
        error(f"PDF export failed: {exc}")


def _use_ai_generate() -> bool:
    return os.environ.get('RESUME_OPTIMIZER_AI_GENERATE', 'false').strip().lower() in (
        '1', 'true', 'yes',
    )


def cmd_generate_fast(text_path: str, analysis_path: str, output_path: str) -> None:
    """Fast path: structure resume from saved text + analysis, no LLM (~5–15s)."""
    if not os.path.isfile(text_path):
        error(f"Text file not found: {text_path}")
    if not os.path.isfile(analysis_path):
        error(f"Analysis file not found: {analysis_path}")

    with open(text_path, 'r', encoding='utf-8') as f:
        extracted_text = f.read()
    with open(analysis_path, 'r', encoding='utf-8') as f:
        suggestions = json.load(f)

    if not extracted_text.strip():
        error("Resume text is empty. Re-upload and analyze your resume first.")

    from fast_builder import build_resume_content
    content = build_resume_content(extracted_text, suggestions)
    _export_pdf(content, output_path)

    print(json.dumps({
        "success": True,
        "output_path": output_path,
        "mode": "fast",
        "data": content,
    }))


def cmd_generate(file_path: str, analysis_path: str, output_path: str) -> None:
    if not os.path.isfile(file_path):
        error(f"File not found: {file_path}")
    if not os.path.isfile(analysis_path):
        error(f"Analysis file not found: {analysis_path}")

    with open(analysis_path, 'r', encoding='utf-8') as f:
        suggestions = json.load(f)

    try:
        extracted_text = extract_resume_text(file_path)
    except Exception as exc:
        error(f"Text extraction failed: {exc}")

    if _use_ai_generate():
        validate_provider_keys()
        try:
            content = generate_optimized_resume(extracted_text, suggestions)
        except SystemExit:
            raise
        except Exception as exc:
            error(f"AI generation failed: {exc}")
    else:
        from fast_builder import build_resume_content
        content = build_resume_content(extracted_text, suggestions)

    _export_pdf(content, output_path)

    print(json.dumps({
        "success": True,
        "output_path": output_path,
        "mode": "ai" if _use_ai_generate() else "fast",
        "data": content,
    }))


def main() -> None:
    if len(sys.argv) < 2:
        error("Usage: optimizer.py analyze <resume_path> | generate <resume_path> <analysis.json> <output.pdf>")

    command = sys.argv[1].lower()

    if command == 'analyze':
        if len(sys.argv) < 3:
            error("Usage: optimizer.py analyze <path_to_resume>")
        cmd_analyze(sys.argv[2])
    elif command == 'generate':
        if len(sys.argv) < 5:
            error("Usage: optimizer.py generate <resume_path> <analysis.json> <output.pdf>")
        cmd_generate(sys.argv[2], sys.argv[3], sys.argv[4])
    elif command == 'generate-fast':
        if len(sys.argv) < 5:
            error("Usage: optimizer.py generate-fast <text.txt> <analysis.json> <output.pdf>")
        cmd_generate_fast(sys.argv[2], sys.argv[3], sys.argv[4])
    else:
        error(f"Unknown command '{command}'. Use 'analyze', 'generate', or 'generate-fast'.")


if __name__ == '__main__':
    main()
