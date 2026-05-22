"""ATS-focused resume analysis."""

from ai_client import call_ai, truncate_text

_ANALYZE_SYSTEM = (
    "You are an expert ATS resume coach. "
    "Return only valid JSON as instructed. No markdown. No explanation. "
    "Use the term ATS-friendly (Applicant Tracking System), never ATC-friendly."
)

_ANALYZE_PROMPT = """
Analyze the resume below for ATS compatibility, formatting, content quality, and professionalism.
Return ONLY a valid JSON object matching this structure (use real data from the resume; empty arrays where none apply):

{{
  "score": 0,
  "summary": "",
  "ats_status": "needs_improvement",
  "ats_issues": [
    {{"issue": "", "severity": "critical", "suggestion": ""}}
  ],
  "formatting_suggestions": [
    {{"title": "", "description": "", "severity": "medium"}}
  ],
  "content_suggestions": [
    {{"title": "", "description": "", "severity": "medium"}}
  ],
  "skills_suggestions": [
    {{"title": "", "description": "", "severity": "medium"}}
  ],
  "experience_suggestions": [
    {{"title": "", "description": "", "severity": "medium"}}
  ],
  "missing_sections": [],
  "recommended_keywords": [],
  "final_recommendations": [
    {{"title": "", "description": "", "severity": "high"}}
  ]
}}

Rules:
- score: integer 0-100 (overall resume quality for ATS and recruiters).
- ats_status: one of "ats_friendly", "needs_improvement", "critical".
- severity on issues/suggestions: "critical", "high", "medium", or "low".
- Mark ATS blockers (tables, images, missing contact, keyword gaps) as severity "critical" in ats_issues.
- recommended_keywords: industry-relevant keywords inferred from the resume (do not invent employers).
- Do not suggest adding fake experience, education, or skills.
- Return ONLY the JSON.

RESUME:
{resume_text}
"""


def analyze_resume(text: str) -> dict:
    prompt = _ANALYZE_PROMPT.format(resume_text=truncate_text(text))
    result = call_ai(prompt, _ANALYZE_SYSTEM)

    score = int(result.get('score', 0))
    score = max(0, min(100, score))

    ats_status = result.get('ats_status', 'needs_improvement')
    if ats_status not in ('ats_friendly', 'needs_improvement', 'critical'):
        if score >= 80:
            ats_status = 'ats_friendly'
        elif score >= 50:
            ats_status = 'needs_improvement'
        else:
            ats_status = 'critical'

    result['score'] = score
    result['ats_status'] = ats_status

    for key in (
        'ats_issues',
        'formatting_suggestions',
        'content_suggestions',
        'skills_suggestions',
        'experience_suggestions',
        'missing_sections',
        'recommended_keywords',
        'final_recommendations',
    ):
        if key not in result or not isinstance(result[key], list):
            result[key] = []

    if 'summary' not in result:
        result['summary'] = ''

    return result
