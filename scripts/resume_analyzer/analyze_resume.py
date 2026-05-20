#!/usr/bin/env python3
"""
Resume Analytics Script
Usage: python3 analyze_resume.py /full/path/to/resume.pdf

Reads AI settings from environment or project root .env:
  RESUME_AI_PROVIDER=groq|openai|gemini (default: groq)
  Groq: GROQ_API_KEY, GROQ_MODEL (OpenAI-compatible API at api.groq.com)
  OpenAI: OPENAI_API_KEY, OPENAI_MODEL
  Gemini: GEMINI_API_KEY, GEMINI_MODEL
Prints a single JSON object to stdout. Never prints API keys.
"""

import sys
import os
import json
import re

# Load .env from the project root (two levels up from this script's directory).
try:
    from dotenv import load_dotenv
    _env_path = os.path.join(os.path.dirname(__file__), '..', '..', '.env')
    load_dotenv(dotenv_path=os.path.abspath(_env_path), override=False)
except ImportError:
    pass  # python-dotenv not installed; fall back to system env


def _error(message: str) -> None:
    print(json.dumps({"success": False, "error": message}))
    sys.exit(1)


def _extract_text_pdf(path: str) -> str:
    import fitz  # PyMuPDF
    text_parts = []
    with fitz.open(path) as doc:
        for page in doc:
            text_parts.append(page.get_text())
    return "\n".join(text_parts)


def _extract_text_docx(path: str) -> str:
    from docx import Document
    doc = Document(path)
    return "\n".join(para.text for para in doc.paragraphs)


def _clean_text(text: str) -> str:
    # Collapse runs of whitespace/blank lines
    text = re.sub(r'\r\n', '\n', text)
    text = re.sub(r'[ \t]+', ' ', text)
    text = re.sub(r'\n{3,}', '\n\n', text)
    return text.strip()


def _extract_text(file_path: str) -> str:
    ext = os.path.splitext(file_path)[1].lower()
    if ext == '.pdf':
        raw = _extract_text_pdf(file_path)
    elif ext == '.docx':
        raw = _extract_text_docx(file_path)
    else:
        _error(f"Unsupported file extension: {ext}. Only pdf and docx are supported.")
    return _clean_text(raw)


_PROMPT_TEMPLATE = """
You are an expert resume analyst and career coach. Analyze the resume below and return ONLY a valid JSON object — no markdown, no explanation, no code fences. The JSON must exactly match this structure (fill every field with real data from the resume; use 0 / [] / "" for fields that cannot be determined):

{{
  "candidate_name": "",
  "email": "",
  "phone": "",
  "current_role": "",
  "total_experience_years": 0,
  "ai_score": 0,
  "top_match_percentage": 0,
  "application_count": 0,
  "skill_count": 0,
  "skills": [],
  "missing_skills": [],
  "skill_gap_analysis": {{
    "labels": [],
    "candidate_scores": [],
    "benchmark_scores": []
  }},
  "career_growth": [
    {{
      "title": "",
      "company": "",
      "duration": "",
      "description": "",
      "level": "",
      "tag": ""
    }}
  ],
  "education": [
    {{
      "institution": "",
      "degree": "",
      "prestige_label": "",
      "score": 0
    }}
  ],
  "nlp_analysis": {{
    "leadership_sentiment": 0,
    "adaptability_score": 0,
    "communication_score": 0,
    "confidence_score": 0
  }},
  "soft_skills": [],
  "ai_profile_summary": "",
  "resume_improvements": [
    {{
      "title": "",
      "description": "",
      "priority": "high"
    }}
  ],
  "job_recommendations": [
    {{
      "job_title": "",
      "match_percentage": 0,
      "reason": ""
    }}
  ],
  "strengths": [],
  "weaknesses": []
}}

Rules:
- ai_score: integer 0–100 representing overall resume quality.
- top_match_percentage: integer 0–100 for the best matching job role.
- skill_gap_analysis.labels: list of skill category names.
- skill_gap_analysis.candidate_scores: list of integers (0–100) matching labels order.
- skill_gap_analysis.benchmark_scores: list of integers (0–100) matching labels order (industry benchmarks).
- nlp_analysis scores: floats 0–100.
- career_growth.level: one of "entry", "mid", "senior", "lead", "executive".
- career_growth.tag: short label, e.g. "Full Stack", "Backend", "Management".
- education.prestige_label: e.g. "Top University", "State College", "Online", "Bootcamp".
- education.score: integer 0–100 representing institution prestige.
- resume_improvements.priority: one of "high", "medium", "low".
- Return ONLY the JSON. No extra text.

RESUME:
{resume_text}
"""

_MAX_TEXT_CHARS = 12000


def _parse_json_response(raw_content: str, provider_label: str) -> dict:
    raw_content = re.sub(r'^```(?:json)?\s*', '', raw_content.strip(), flags=re.IGNORECASE)
    raw_content = re.sub(r'\s*```$', '', raw_content.strip())

    try:
        return json.loads(raw_content)
    except json.JSONDecodeError:
        match = re.search(r'\{[\s\S]*\}', raw_content)
        if match:
            return json.loads(match.group(0))
        _error(f"{provider_label} returned a response that could not be parsed as JSON.")


def _build_prompt(resume_text: str) -> str:
    if len(resume_text) > _MAX_TEXT_CHARS:
        resume_text = resume_text[:_MAX_TEXT_CHARS]
    return _PROMPT_TEMPLATE.format(resume_text=resume_text)


def _ai_provider() -> str:
    provider = (os.environ.get('RESUME_AI_PROVIDER') or 'groq').strip().lower()
    # Common typos for Groq
    if provider in ('grock', 'grok'):
        return 'groq'
    return provider


def _call_gemini(resume_text: str) -> dict:
    import google.generativeai as genai

    api_key = os.environ.get('GEMINI_API_KEY', '').strip()
    model_name = os.environ.get('GEMINI_MODEL', 'gemini-2.0-flash').strip()

    if not api_key:
        _error("GEMINI_API_KEY is not set. Get a key at https://aistudio.google.com/apikey")

    genai.configure(api_key=api_key)
    model = genai.GenerativeModel(
        model_name,
        system_instruction=(
            "You are a professional resume analyst. "
            "Return only valid JSON as instructed. No markdown. No explanation."
        ),
    )

    prompt = _build_prompt(resume_text)
    response = model.generate_content(
        prompt,
        generation_config={
            'temperature': 0.2,
            'max_output_tokens': 4096,
        },
    )

    raw_content = (response.text or "").strip()
    if not raw_content:
        _error("Gemini returned an empty response.")

    return _parse_json_response(raw_content, "Gemini")


def _chat_completion_json(resume_text: str, *, api_key: str, model: str, base_url: str | None, label: str) -> dict:
    """Shared OpenAI-compatible chat API (OpenAI, Groq, etc.)."""
    from openai import OpenAI

    if not api_key:
        _error(f"API key for {label} is not set in environment.")

    kwargs = {'api_key': api_key}
    if base_url:
        kwargs['base_url'] = base_url

    client = OpenAI(**kwargs)
    prompt = _build_prompt(resume_text)

    response = client.chat.completions.create(
        model=model,
        messages=[
            {
                "role": "system",
                "content": (
                    "You are a professional resume analyst. "
                    "Return only valid JSON as instructed. No markdown. No explanation."
                ),
            },
            {"role": "user", "content": prompt},
        ],
        temperature=0.2,
        max_tokens=4096,
    )

    raw_content = response.choices[0].message.content or ""
    return _parse_json_response(raw_content, label)


def _call_groq(resume_text: str) -> dict:
    return _chat_completion_json(
        resume_text,
        api_key=os.environ.get('GROQ_API_KEY', '').strip(),
        model=os.environ.get('GROQ_MODEL', 'llama-3.3-70b-versatile').strip(),
        base_url=os.environ.get('GROQ_BASE_URL', 'https://api.groq.com/openai/v1').strip(),
        label='Groq',
    )


def _call_openai(resume_text: str) -> dict:
    return _chat_completion_json(
        resume_text,
        api_key=os.environ.get('OPENAI_API_KEY', '').strip(),
        model=os.environ.get('OPENAI_MODEL', 'gpt-4.1-mini').strip(),
        base_url=None,
        label='OpenAI',
    )


def _call_ai(resume_text: str) -> dict:
    provider = _ai_provider()
    if provider == 'groq':
        return _call_groq(resume_text)
    if provider == 'gemini':
        return _call_gemini(resume_text)
    if provider == 'openai':
        return _call_openai(resume_text)
    _error(f"Unknown RESUME_AI_PROVIDER '{provider}'. Use 'groq', 'openai', or 'gemini'.")


def main():
    if len(sys.argv) < 2:
        _error("Usage: analyze_resume.py <path_to_resume>")

    file_path = sys.argv[1]

    # Validate file exists
    if not os.path.isfile(file_path):
        _error(f"File not found: {file_path}")

    # Validate extension
    ext = os.path.splitext(file_path)[1].lower()
    if ext not in ('.pdf', '.docx'):
        _error(f"Unsupported file type '{ext}'. Only .pdf and .docx are accepted.")

    provider = _ai_provider()
    if provider == 'groq':
        if not os.environ.get('GROQ_API_KEY', '').strip():
            _error("GROQ_API_KEY is not set. Get a key at https://console.groq.com/keys")
    elif provider == 'gemini':
        if not os.environ.get('GEMINI_API_KEY', '').strip():
            _error("GEMINI_API_KEY is not set. Add it to .env or set RESUME_AI_PROVIDER=groq.")
    elif provider == 'openai':
        if not os.environ.get('OPENAI_API_KEY', '').strip():
            _error("OPENAI_API_KEY is not set. Add it to .env or set RESUME_AI_PROVIDER=groq.")
    else:
        _error(f"Unknown RESUME_AI_PROVIDER '{provider}'. Use 'groq', 'openai', or 'gemini'.")

    # Extract text
    try:
        extracted_text = _extract_text(file_path)
    except Exception as exc:
        _error(f"Text extraction failed: {exc}")

    if not extracted_text.strip():
        _error("No text could be extracted from the resume. The file may be a scanned image PDF.")

    try:
        analysis = _call_ai(extracted_text)
    except SystemExit:
        raise
    except Exception as exc:
        labels = {'groq': 'Groq', 'gemini': 'Gemini', 'openai': 'OpenAI'}
        label = labels.get(provider, provider)
        _error(f"{label} API call failed: {exc}")

    # Output success response
    print(json.dumps({
        "success": True,
        "data": analysis,
        "extracted_text": extracted_text,
    }))


if __name__ == '__main__':
    main()
