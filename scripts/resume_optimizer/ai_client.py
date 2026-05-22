"""Shared AI provider helpers for resume optimizer."""

import json
import os
import re
import sys


def error(message: str) -> None:
    print(json.dumps({"success": False, "error": message}))
    sys.exit(1)


def ai_provider() -> str:
    provider = (os.environ.get('RESUME_AI_PROVIDER') or 'groq').strip().lower()
    if provider in ('grock', 'grok'):
        return 'groq'
    return provider


def validate_provider_keys() -> str:
    provider = ai_provider()
    if provider == 'groq':
        if not os.environ.get('GROQ_API_KEY', '').strip():
            error("GROQ_API_KEY is not set. Get a key at https://console.groq.com/keys")
    elif provider == 'gemini':
        if not os.environ.get('GEMINI_API_KEY', '').strip():
            error("GEMINI_API_KEY is not set. Add it to .env or set RESUME_AI_PROVIDER=groq.")
    elif provider == 'openai':
        if not os.environ.get('OPENAI_API_KEY', '').strip():
            error("OPENAI_API_KEY is not set. Add it to .env or set RESUME_AI_PROVIDER=groq.")
    else:
        error(f"Unknown RESUME_AI_PROVIDER '{provider}'. Use 'groq', 'openai', or 'gemini'.")
    return provider


def parse_json_response(raw_content: str, provider_label: str) -> dict:
    raw_content = re.sub(r'^```(?:json)?\s*', '', raw_content.strip(), flags=re.IGNORECASE)
    raw_content = re.sub(r'\s*```$', '', raw_content.strip())
    try:
        return json.loads(raw_content)
    except json.JSONDecodeError:
        match = re.search(r'\{[\s\S]*\}', raw_content)
        if match:
            return json.loads(match.group(0))
        error(f"{provider_label} returned a response that could not be parsed as JSON.")


_MAX_TEXT_CHARS = 14000


def truncate_text(text: str) -> str:
    if len(text) > _MAX_TEXT_CHARS:
        return text[:_MAX_TEXT_CHARS]
    return text


def call_gemini(prompt: str, system: str) -> dict:
    import google.generativeai as genai

    api_key = os.environ.get('GEMINI_API_KEY', '').strip()
    model_name = os.environ.get('GEMINI_MODEL', 'gemini-2.0-flash').strip()
    if not api_key:
        error("GEMINI_API_KEY is not set.")

    genai.configure(api_key=api_key)
    model = genai.GenerativeModel(model_name, system_instruction=system)
    response = model.generate_content(
        prompt,
        generation_config={'temperature': 0.2, 'max_output_tokens': 8192},
    )
    raw = (response.text or "").strip()
    if not raw:
        error("Gemini returned an empty response.")
    return parse_json_response(raw, "Gemini")


def chat_completion_json(prompt: str, system: str, *, api_key: str, model: str, base_url: str | None, label: str) -> dict:
    from openai import OpenAI

    if not api_key:
        error(f"API key for {label} is not set in environment.")

    kwargs = {'api_key': api_key}
    if base_url:
        kwargs['base_url'] = base_url

    client = OpenAI(**kwargs)
    response = client.chat.completions.create(
        model=model,
        messages=[
            {"role": "system", "content": system},
            {"role": "user", "content": prompt},
        ],
        temperature=0.2,
        max_tokens=8192,
    )
    raw = response.choices[0].message.content or ""
    return parse_json_response(raw, label)


def call_ai(prompt: str, system: str) -> dict:
    provider = ai_provider()
    if provider == 'groq':
        return chat_completion_json(
            prompt,
            system,
            api_key=os.environ.get('GROQ_API_KEY', '').strip(),
            model=os.environ.get('GROQ_MODEL', 'llama-3.3-70b-versatile').strip(),
            base_url=os.environ.get('GROQ_BASE_URL', 'https://api.groq.com/openai/v1').strip(),
            label='Groq',
        )
    if provider == 'gemini':
        return call_gemini(prompt, system)
    if provider == 'openai':
        return chat_completion_json(
            prompt,
            system,
            api_key=os.environ.get('OPENAI_API_KEY', '').strip(),
            model=os.environ.get('OPENAI_MODEL', 'gpt-4.1-mini').strip(),
            base_url=None,
            label='OpenAI',
        )
    error(f"Unknown RESUME_AI_PROVIDER '{provider}'.")
