#!/usr/bin/env python3
"""
Job Description Analyzer
Usage: python3 analyze_jd.py <path_to_jd.txt|.pdf|.docx>

Reads AI settings from RESUME_AI_PROVIDER (groq|openai|gemini).
Prints JSON: {"success": true, "data": {...requirements...}}
"""

import sys
import os
import json
import re

try:
    from dotenv import load_dotenv
    _env_path = os.path.join(os.path.dirname(__file__), '..', '..', '.env')
    load_dotenv(dotenv_path=os.path.abspath(_env_path), override=False)
except ImportError:
    pass


def _error(message: str) -> None:
    print(json.dumps({"success": False, "error": message}))
    sys.exit(1)


def _extract_text_pdf(path: str) -> str:
    import fitz
    parts = []
    with fitz.open(path) as doc:
        for page in doc:
            parts.append(page.get_text())
    return "\n".join(parts)


def _extract_text_docx(path: str) -> str:
    from docx import Document
    doc = Document(path)
    return "\n".join(para.text for para in doc.paragraphs)


def _extract_text(path: str) -> str:
    ext = os.path.splitext(path)[1].lower()
    if ext == '.pdf':
        raw = _extract_text_pdf(path)
    elif ext == '.docx':
        raw = _extract_text_docx(path)
    elif ext in ('.txt', ''):
        with open(path, 'r', encoding='utf-8', errors='replace') as f:
            raw = f.read()
    else:
        _error(f"Unsupported file extension: {ext}")
    text = re.sub(r'\r\n', '\n', raw)
    text = re.sub(r'[ \t]+', ' ', text)
    text = re.sub(r'\n{3,}', '\n\n', text)
    return text.strip()


_PROMPT = """
You are an expert HR analyst. Analyze the job description below and return ONLY valid JSON (no markdown) matching:

{{
  "skills": ["skill1", "skill2"],
  "experience": "e.g. 5+ years in backend development",
  "education": "e.g. Bachelor's in Computer Science",
  "technologies": ["React", "Node.js"],
  "responsibilities": ["responsibility 1", "responsibility 2"],
  "preferred_qualifications": ["qualification 1"],
  "keywords": ["keyword1", "keyword2"]
}}

Rules:
- Extract real requirements from the JD; use [] or "" if not stated.
- skills and technologies: concrete tools/languages/frameworks.
- responsibilities: 3-8 bullet-style strings.
- keywords: important terms for matching (roles, domains, methods).
- Return ONLY the JSON object.

JOB DESCRIPTION:
{jd_text}
"""

_MAX_CHARS = 14000


def _parse_json_response(raw_content: str, label: str) -> dict:
    raw_content = re.sub(r'^```(?:json)?\s*', '', raw_content.strip(), flags=re.IGNORECASE)
    raw_content = re.sub(r'\s*```$', '', raw_content.strip())
    try:
        return json.loads(raw_content)
    except json.JSONDecodeError:
        match = re.search(r'\{[\s\S]*\}', raw_content)
        if match:
            return json.loads(match.group(0))
        _error(f"{label} returned unparseable JSON.")


def _ai_provider() -> str:
    provider = (os.environ.get('RESUME_AI_PROVIDER') or 'groq').strip().lower()
    if provider in ('grock', 'grok'):
        return 'groq'
    return provider


def _call_gemini(jd_text: str) -> dict:
    import google.generativeai as genai
    api_key = os.environ.get('GEMINI_API_KEY', '').strip()
    if not api_key:
        _error("GEMINI_API_KEY is not set.")
    genai.configure(api_key=api_key)
    model = genai.GenerativeModel(
        os.environ.get('GEMINI_MODEL', 'gemini-2.0-flash').strip(),
        system_instruction="Return only valid JSON. No markdown.",
    )
    prompt = _PROMPT.format(jd_text=jd_text[:_MAX_CHARS])
    response = model.generate_content(prompt, generation_config={'temperature': 0.2, 'max_output_tokens': 2048})
    raw = (response.text or "").strip()
    if not raw:
        _error("Gemini returned empty response.")
    return _parse_json_response(raw, "Gemini")


def _chat_json(jd_text: str, *, api_key: str, model: str, base_url: str | None, label: str) -> dict:
    from openai import OpenAI
    if not api_key:
        _error(f"API key for {label} is not set.")
    kwargs = {'api_key': api_key}
    if base_url:
        kwargs['base_url'] = base_url
    client = OpenAI(**kwargs)
    prompt = _PROMPT.format(jd_text=jd_text[:_MAX_CHARS])
    response = client.chat.completions.create(
        model=model,
        messages=[
            {"role": "system", "content": "Return only valid JSON. No markdown."},
            {"role": "user", "content": prompt},
        ],
        temperature=0.2,
        max_tokens=2048,
    )
    raw = response.choices[0].message.content or ""
    return _parse_json_response(raw, label)


def _call_ai(jd_text: str) -> dict:
    provider = _ai_provider()
    if provider == 'groq':
        return _chat_json(
            jd_text,
            api_key=os.environ.get('GROQ_API_KEY', '').strip(),
            model=os.environ.get('GROQ_MODEL', 'llama-3.3-70b-versatile').strip(),
            base_url=os.environ.get('GROQ_BASE_URL', 'https://api.groq.com/openai/v1').strip(),
            label='Groq',
        )
    if provider == 'gemini':
        return _call_gemini(jd_text)
    if provider == 'openai':
        return _chat_json(
            jd_text,
            api_key=os.environ.get('OPENAI_API_KEY', '').strip(),
            model=os.environ.get('OPENAI_MODEL', 'gpt-4.1-mini').strip(),
            base_url=None,
            label='OpenAI',
        )
    _error(f"Unknown RESUME_AI_PROVIDER '{provider}'.")


def main():
    if len(sys.argv) < 2:
        _error("Usage: analyze_jd.py <path_to_jd_file>")

    file_path = sys.argv[1]
    if not os.path.isfile(file_path):
        _error(f"File not found: {file_path}")

    provider = _ai_provider()
    if provider == 'groq' and not os.environ.get('GROQ_API_KEY', '').strip():
        _error("GROQ_API_KEY is not set.")
    elif provider == 'gemini' and not os.environ.get('GEMINI_API_KEY', '').strip():
        _error("GEMINI_API_KEY is not set.")
    elif provider == 'openai' and not os.environ.get('OPENAI_API_KEY', '').strip():
        _error("OPENAI_API_KEY is not set.")

    try:
        jd_text = _extract_text(file_path)
    except Exception as exc:
        _error(f"Text extraction failed: {exc}")

    if not jd_text.strip():
        _error("No text could be extracted from the job description.")

    try:
        data = _call_ai(jd_text)
    except SystemExit:
        raise
    except Exception as exc:
        _error(f"AI analysis failed: {exc}")

    print(json.dumps({"success": True, "data": data, "extracted_text": jd_text}))


if __name__ == '__main__':
    main()
