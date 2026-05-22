"""Build resume PDF content from extracted text + analysis (no LLM — fast path)."""

import re
from typing import Any


def _extract_contact(text: str) -> dict:
    email = ''
    phone = ''
    match = re.search(r'[\w.+-]+@[\w-]+\.[\w.-]+', text)
    if match:
        email = match.group(0)
    match = re.search(r'(\+?\d[\d\s().-]{8,}\d)', text)
    if match:
        phone = match.group(0).strip()

    lines = [ln.strip() for ln in text.split('\n') if ln.strip()]
    name = lines[0] if lines else 'Resume'

    return {
        'name': name[:80],
        'email': email,
        'phone': phone,
        'location': '',
        'linkedin': '',
    }


def _split_sections(text: str) -> list[tuple[str, list[str]]]:
    headers = re.compile(
        r'^(profile|summary|objective|experience|work experience|employment|'
        r'education|skills|technical skills|projects|certifications|achievements)\s*:?\s*$',
        re.I,
    )
    sections: list[tuple[str, list[str]]] = []
    current_title = 'Summary'
    current_lines: list[str] = []

    for line in text.split('\n'):
        stripped = line.strip()
        if not stripped:
            if current_lines:
                current_lines.append('')
            continue
        if headers.match(stripped):
            if current_lines:
                sections.append((current_title, current_lines))
            current_title = stripped.rstrip(':').title()
            current_lines = []
        else:
            current_lines.append(stripped)

    if current_lines:
        sections.append((current_title, current_lines))

    return sections


def _lines_to_bullets(lines: list[str]) -> list[str]:
    bullets = []
    for line in lines:
        line = re.sub(r'^[\-\*\u2022]\s*', '', line).strip()
        if line and len(line) > 2:
            bullets.append(line)
    return bullets[:12]


def build_resume_content(text: str, suggestions: dict) -> dict:
    contact = _extract_contact(text)
    sections = _split_sections(text)

    summary = (suggestions.get('summary') or '').strip()
    experience: list[dict] = []
    education: list[dict] = []
    skills: list[str] = []
    projects: list[dict] = []

    for title, lines in sections:
        lower = title.lower()
        body = '\n'.join(lines).strip()
        if not body:
            continue

        if lower in ('summary', 'profile', 'objective'):
            if not summary:
                summary = body[:1200]
        elif lower in ('experience', 'work experience', 'employment'):
            bullets = _lines_to_bullets(lines)
            if bullets:
                experience.append({
                    'title': '',
                    'company': '',
                    'dates': '',
                    'bullets': bullets,
                })
            else:
                experience.append({
                    'title': lines[0][:100] if lines else '',
                    'company': lines[1][:100] if len(lines) > 1 else '',
                    'dates': '',
                    'bullets': _lines_to_bullets(lines[2:]) or [body[:500]],
                })
        elif lower == 'education':
            education.append({
                'degree': body[:200],
                'institution': '',
                'dates': '',
                'details': '',
            })
        elif 'skill' in lower:
            joined = ' '.join(lines)
            parts = re.split(r'[,;|•\n]', joined)
            skills.extend(p.strip() for p in parts if p.strip())
        elif lower == 'projects':
            projects.append({
                'name': lines[0][:100] if lines else 'Projects',
                'description': '',
                'bullets': _lines_to_bullets(lines[1:]) or [body[:400]],
            })

    keywords = suggestions.get('recommended_keywords') or []
    for kw in keywords:
        if isinstance(kw, str) and kw.strip() and kw.strip() not in skills:
            skills.append(kw.strip())
        elif isinstance(kw, dict) and kw.get('keyword'):
            k = str(kw['keyword']).strip()
            if k and k not in skills:
                skills.append(k)

    if not summary and suggestions.get('summary'):
        summary = str(suggestions['summary'])[:1200]

    if not experience and sections:
        for title, lines in sections:
            if title.lower() not in ('summary', 'profile', 'skills', 'education') and lines:
                experience.append({
                    'title': title,
                    'company': '',
                    'dates': '',
                    'bullets': _lines_to_bullets(lines),
                })

    if not experience:
        chunks = [ln.strip() for ln in text.split('\n') if ln.strip()][1:20]
        if chunks:
            experience.append({
                'title': 'Experience',
                'company': '',
                'dates': '',
                'bullets': chunks[:10],
            })

    return {
        'contact': contact,
        'summary': summary,
        'experience': experience,
        'education': education,
        'skills': skills[:40],
        'projects': projects,
    }
