#!/usr/bin/env python3
"""
Explain why a candidate matches a JD.
Usage: python3 explain_match.py <jd_summary.json path> <candidate_summary.json path>
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


def _ai_provider() -> str:
    provider = (os.environ.get('RESUME_AI_PROVIDER') or 'groq').strip().lower()
    if provider in ('grock', 'grok'):
        return 'groq'
    return provider


def _call_ai(jd_summary: str, candidate_summary: str) -> str:
    provider = _ai_provider()
    prompt = (
        "In one or two concise sentences (max 200 chars), explain why this candidate is a good fit "
        "for the job. Be specific about skills and experience. No markdown.\n\n"
        f"JOB:\n{jd_summary}\n\nCANDIDATE:\n{candidate_summary}"
    )

    if provider == 'gemini':
        import google.generativeai as genai
        api_key = os.environ.get('GEMINI_API_KEY', '').strip()
        if not api_key:
            _error("GEMINI_API_KEY is not set.")
        genai.configure(api_key=api_key)
        model = genai.GenerativeModel(os.environ.get('GEMINI_MODEL', 'gemini-2.0-flash').strip())
        response = model.generate_content(prompt, generation_config={'temperature': 0.3, 'max_output_tokens': 120})
        return (response.text or "").strip()[:300]

    from openai import OpenAI
    if provider == 'groq':
        api_key = os.environ.get('GROQ_API_KEY', '').strip()
        model = os.environ.get('GROQ_MODEL', 'llama-3.3-70b-versatile').strip()
        base_url = os.environ.get('GROQ_BASE_URL', 'https://api.groq.com/openai/v1').strip()
    else:
        api_key = os.environ.get('OPENAI_API_KEY', '').strip()
        model = os.environ.get('OPENAI_MODEL', 'gpt-4.1-mini').strip()
        base_url = None

    if not api_key:
        _error("API key is not set.")

    kwargs = {'api_key': api_key}
    if base_url:
        kwargs['base_url'] = base_url
    client = OpenAI(**kwargs)
    response = client.chat.completions.create(
        model=model,
        messages=[{"role": "user", "content": prompt}],
        temperature=0.3,
        max_tokens=120,
    )
    return (response.choices[0].message.content or "").strip()[:300]


def main():
    if len(sys.argv) < 3:
        _error("Usage: explain_match.py <jd.json> <candidate.json>")

    with open(sys.argv[1], 'r', encoding='utf-8') as f:
        jd = f.read()
    with open(sys.argv[2], 'r', encoding='utf-8') as f:
        candidate = f.read()

    try:
        reason = _call_ai(jd, candidate)
    except SystemExit:
        raise
    except Exception as exc:
        _error(str(exc))

    if not reason:
        _error("Empty explanation from AI.")

    print(json.dumps({"success": True, "reason": reason}))


if __name__ == '__main__':
    main()
