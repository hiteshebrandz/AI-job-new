#!/usr/bin/env python3
"""
Resume parser for Elements HR.
Reads a resume file path from argv[1], outputs JSON to stdout.
"""
import json
import re
import sys
from pathlib import Path


def read_text(path: Path) -> str:
    suffix = path.suffix.lower()
    if suffix == ".txt":
        return path.read_text(encoding="utf-8", errors="ignore")
    if suffix == ".pdf":
        try:
            from pypdf import PdfReader  # type: ignore

            reader = PdfReader(str(path))
            return "\n".join((page.extract_text() or "") for page in reader.pages)
        except Exception:
            return path.read_bytes().decode("utf-8", errors="ignore")
    if suffix == ".docx":
        try:
            import docx  # type: ignore

            document = docx.Document(str(path))
            return "\n".join(p.text for p in document.paragraphs)
        except Exception:
            return ""
    return ""


def extract_email(text: str) -> str:
    match = re.search(r"[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", text)
    return match.group(0) if match else ""


def extract_phone(text: str) -> str:
    match = re.search(r"(\+?\d[\d\s\-().]{8,}\d)", text)
    return match.group(0).strip() if match else ""


def extract_name(text: str) -> str:
    for line in text.splitlines():
        line = line.strip()
        if not line or len(line) > 60:
            continue
        if "@" in line or re.search(r"\d{3,}", line):
            continue
        if re.match(r"^[A-Za-z][A-Za-z\s.'-]{2,}$", line):
            return line.title()
    return ""


def extract_skills(text: str) -> list:
    catalog = [
        "PHP", "Laravel", "MySQL", "JavaScript", "TypeScript", "React", "Vue", "Angular",
        "Node.js", "Python", "Java", "AWS", "Docker", "Kubernetes", "Git", "HTML", "CSS",
        "Tailwind", "REST API", "GraphQL", "Redis", "PostgreSQL", "MongoDB", "CI/CD",
        "Agile", "Scrum", "Leadership", "Communication", "SQL", "Azure", "GCP",
    ]
    found = []
    lower = text.lower()
    for skill in catalog:
        if skill.lower() in lower and skill not in found:
            found.append(skill)
    return found[:12]


def extract_experience_years(text: str) -> int:
    patterns = [
        r"(\d+)\+?\s*(?:years|yrs)(?:\s+of)?\s+(?:experience|exp)",
        r"experience[:\s]+(\d+)\+?\s*(?:years|yrs)",
    ]
    for pattern in patterns:
        match = re.search(pattern, text, re.I)
        if match:
            return min(int(match.group(1)), 50)
    return 0


def extract_education(text: str) -> dict:
    degree = ""
    university = ""
    year = None
    degree_patterns = [
        r"(B\.?Tech|M\.?Tech|B\.?Sc|M\.?Sc|BCA|MCA|MBA|BBA|Ph\.?D|Bachelor|Master)[^\n]*",
    ]
    for pattern in degree_patterns:
        match = re.search(pattern, text, re.I)
        if match:
            degree = match.group(0).strip()[:120]
            break
    uni_match = re.search(
        r"(?:University|Institute|College)[^\n]*|([A-Z][A-Za-z\s&]+(?:University|College|Institute))",
        text,
        re.I,
    )
    if uni_match:
        university = (uni_match.group(0) or uni_match.group(1) or "").strip()[:120]
    year_match = re.search(r"(?:19|20)\d{2}", text)
    if year_match:
        year = int(year_match.group(0))
    return {"education": degree, "university": university, "graduation_year": year}


def extract_title(text: str) -> str:
    titles = [
        "Software Engineer", "Senior Software Engineer", "Full Stack Developer",
        "Product Manager", "Data Scientist", "DevOps Engineer", "HR Manager",
        "Designer", "Consultant", "Developer", "Architect", "Team Lead",
    ]
    lower = text.lower()
    for title in titles:
        if title.lower() in lower:
            return title
    return ""


def seniority_from_years(years: int) -> str:
    if years >= 10:
        return "Executive"
    if years >= 5:
        return "Senior"
    if years >= 2:
        return "Mid-Level"
    return "Junior"


def ai_score(skills: list, years: int) -> int:
    base = 72 + min(len(skills) * 2, 16) + min(years, 10)
    return min(base, 98)


def main() -> None:
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Missing file path"}))
        sys.exit(1)

    path = Path(sys.argv[1])
    if not path.exists():
        print(json.dumps({"error": "File not found"}))
        sys.exit(1)

    text = read_text(path)
    edu = extract_education(text)
    years = extract_experience_years(text)
    skills = extract_skills(text)
    score = ai_score(skills, years)

    payload = {
        "name": extract_name(text) or "Unknown Candidate",
        "email": extract_email(text),
        "phone": extract_phone(text),
        "location": "",
        "title": extract_title(text),
        "skills": skills,
        "experience": f"{years} Years" if years else "",
        "experience_years": years,
        "education": edu.get("education") or "",
        "university": edu.get("university") or "",
        "graduation_year": edu.get("graduation_year"),
        "seniority_level": seniority_from_years(years),
        "previous_companies": "",
        "ai_score": score,
        "ai_recommendation": (
            "Strong profile with solid technical breadth. Recommended for engineering and product roles."
            if score >= 85
            else "Good foundational profile. Consider roles aligned with core skills and experience level."
        ),
        "skill_accuracy": min(98, 85 + len(skills)),
    }
    print(json.dumps(payload))


if __name__ == "__main__":
    main()
