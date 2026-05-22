"""Generate optimized resume content from original text and analysis."""

import json

from ai_client import call_ai, truncate_text

_GENERATE_SYSTEM = (
    "You are a professional resume writer focused on ATS-friendly resumes. "
    "Return only valid JSON as instructed. No markdown. No explanation. "
    "Never invent employers, job titles, degrees, certifications, or skills not supported by the original resume."
)

_GENERATE_PROMPT = """
Using ONLY facts from the original resume below, produce an improved ATS-friendly resume.
Apply the analysis suggestions where appropriate. Improve bullet points, grammar, clarity, and keywords.
Do NOT add fake experience, fake education, fake projects, or fake skills.

Return ONLY this JSON structure:

{{
  "contact": {{
    "name": "",
    "email": "",
    "phone": "",
    "location": "",
    "linkedin": ""
  }},
  "summary": "",
  "experience": [
    {{
      "title": "",
      "company": "",
      "dates": "",
      "bullets": [""]
    }}
  ],
  "education": [
    {{
      "degree": "",
      "institution": "",
      "dates": "",
      "details": ""
    }}
  ],
  "skills": [],
  "projects": [
    {{
      "name": "",
      "description": "",
      "bullets": [""]
    }}
  ]
}}

ORIGINAL RESUME:
{resume_text}

ANALYSIS SUGGESTIONS (JSON):
{suggestions_json}
"""


def generate_optimized_resume(text: str, suggestions: dict) -> dict:
    suggestions_json = json.dumps(suggestions, ensure_ascii=False)
    prompt = _GENERATE_PROMPT.format(
        resume_text=truncate_text(text),
        suggestions_json=suggestions_json[:8000],
    )
    result = call_ai(prompt, _GENERATE_SYSTEM)

    for key in ('contact', 'experience', 'education', 'skills', 'projects'):
        if key not in result:
            if key == 'contact':
                result[key] = {}
            elif key == 'skills':
                result[key] = []
            else:
                result[key] = []

    if 'summary' not in result:
        result['summary'] = ''

    return result
