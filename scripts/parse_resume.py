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
        text = _read_pdf_pdfplumber(path)
        if text.strip():
            return text
        return _read_pdf_pypdf(path)
    if suffix == ".docx":
        try:
            import docx  # type: ignore
            document = docx.Document(str(path))
            return "\n".join(p.text for p in document.paragraphs)
        except Exception:
            return ""
    return ""


def _read_pdf_pdfplumber(path: Path) -> str:
    try:
        import pdfplumber  # type: ignore
        parts = []
        with pdfplumber.open(str(path)) as pdf:
            for page in pdf.pages:
                parts.append(page.extract_text() or "")
        return "\n".join(parts)
    except Exception:
        return ""


def _read_pdf_pypdf(path: Path) -> str:
    try:
        from pypdf import PdfReader  # type: ignore
        reader = PdfReader(str(path))
        return "\n".join((page.extract_text() or "") for page in reader.pages)
    except Exception:
        return path.read_bytes().decode("utf-8", errors="ignore")


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


def extract_location(text: str) -> str:
    """Extract city/state/country from common resume patterns."""
    patterns = [
        r"(?:location|address|based in|city|residing in|located in)\s*[:\-]?\s*([A-Za-z ,.\-]+?)(?:\n|$)",
        r"([A-Za-z ]+,\s*[A-Z]{2}(?:\s+\d{5})?)",  # City, ST  or  City, ST 12345
        r"([A-Za-z ]+,\s*[A-Za-z ]+,\s*[A-Za-z ]+)",  # City, State, Country
    ]
    for pat in patterns:
        m = re.search(pat, text, re.I)
        if m:
            loc = m.group(1).strip().rstrip(",")
            if 3 < len(loc) < 80:
                return loc
    return ""


def extract_previous_companies(text: str) -> str:
    """Extract company names from experience section."""
    companies = []

    # Look for experience section
    exp_match = re.search(
        r"(?:work\s+experience|experience|employment\s+history)\s*[:\-]?\s*\n(.*?)(?=\n(?:education|skills|certifications|projects|references)\b|\Z)",
        text,
        re.I | re.S,
    )
    exp_text = exp_match.group(1) if exp_match else text

    # Patterns: "CompanyName | Role", "at CompanyName", "@ CompanyName"
    patterns = [
        r"(?:^|\n)\s*([A-Z][A-Za-z0-9\s&',.\-]{2,40})\s*[\|·•]\s*[A-Za-z]",  # Company | Role
        r"(?:at|@)\s+([A-Z][A-Za-z0-9\s&',.\-]{2,40}?)(?:\s*[,|\n])",
        r"(?:^|\n)\s*([A-Z][A-Za-z0-9\s&',.\-]{2,30}(?:Inc|LLC|Ltd|Corp|Co|Solutions|Technologies|Systems|Services|Group|Labs|Agency)\.?)\s*\n",
    ]

    for pat in patterns:
        for m in re.finditer(pat, exp_text, re.M):
            company = m.group(1).strip().rstrip(",.")
            if company and company not in companies and len(company) > 2:
                companies.append(company)
            if len(companies) >= 5:
                break

    return ", ".join(companies[:5])


def extract_skills(text: str) -> list:
    catalog = [
        # Programming languages
        "PHP", "Python", "Java", "JavaScript", "TypeScript", "C#", "C++", "Ruby", "Go",
        "Swift", "Kotlin", "Rust", "Scala", "R", "MATLAB", "Perl", "Shell", "Bash",
        # Web frameworks / libraries
        "Laravel", "Django", "Flask", "FastAPI", "Spring Boot", "ASP.NET", "Rails",
        "React", "Vue", "Angular", "Next.js", "Nuxt.js", "Svelte", "jQuery",
        "Tailwind", "Bootstrap", "HTML", "CSS", "SASS", "LESS",
        # Databases
        "MySQL", "PostgreSQL", "MongoDB", "Redis", "SQLite", "MariaDB", "Oracle",
        "SQL Server", "DynamoDB", "Cassandra", "Elasticsearch",
        # Cloud & DevOps
        "AWS", "Azure", "GCP", "Docker", "Kubernetes", "Terraform", "Ansible",
        "Jenkins", "GitLab CI", "GitHub Actions", "CircleCI", "CI/CD",
        "Linux", "Nginx", "Apache", "Heroku", "Vercel",
        # APIs & Integration
        "REST API", "GraphQL", "gRPC", "WebSockets", "OAuth", "JWT", "Sanctum",
        "Microservices", "SOAP", "RabbitMQ", "Kafka", "Celery",
        # Tools & Methodologies
        "Git", "GitHub", "Bitbucket", "Jira", "Confluence", "Agile", "Scrum",
        "Kanban", "TDD", "BDD", "CI/CD", "DevOps",
        # Data & AI/ML
        "Machine Learning", "Deep Learning", "TensorFlow", "PyTorch", "scikit-learn",
        "Pandas", "NumPy", "Tableau", "Power BI", "Data Analysis", "NLP",
        "Computer Vision", "Jupyter", "Spark", "Hadoop",
        # Business / Finance
        "Accounting", "Bookkeeping", "QuickBooks", "SAP", "Excel", "Financial Reporting",
        "Tax Preparation", "Auditing", "Payroll", "GAAP", "Budgeting", "Forecasting",
        "Oracle Financials", "Accounts Payable", "Accounts Receivable", "Reconciliation",
        "ERP", "CRM", "Salesforce", "HubSpot",
        # Soft skills
        "Leadership", "Communication", "Project Management", "Problem Solving",
        "Team Management", "Mentoring", "Stakeholder Management",
    ]
    found = []
    lower = text.lower()
    for skill in catalog:
        if skill.lower() in lower and skill not in found:
            found.append(skill)
    return found[:15]


def extract_experience_years(text: str) -> int:
    patterns = [
        r"(\d+)\+?\s*(?:years|yrs)(?:\s+of)?\s+(?:experience|exp)",
        r"experience[:\s]+(\d+)\+?\s*(?:years|yrs)",
        r"(\d+)\s*(?:years|yrs)\s+(?:of\s+)?(?:professional|industry|relevant)",
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
        r"(Ph\.?D\.?(?:[,\s][^\n]*)?)",
        r"(M\.?(?:Tech|Sc|S|A|B|BA|CA|Phil|Eng)\.?(?:[,\s][^\n]*)?)",
        r"(B\.?(?:Tech|Sc|S|A|E|Com|CA|Arch|Pharm)\.?(?:[,\s][^\n]*)?)",
        r"((?:Bachelor|Master|Doctor)(?:'s)?(?:[,\s][^\n]*)?)",
        r"(MBA|BBA|BCA|MCA|PGDM)(?:[,\s][^\n]*)?",
    ]
    for pattern in degree_patterns:
        match = re.search(pattern, text, re.I)
        if match:
            degree = match.group(0).strip()[:120]
            break

    uni_match = re.search(
        r"(?:^|\n)\s*([A-Z][A-Za-z\s&'.\-]+(?:University|College|Institute|School|Academy))\b",
        text,
        re.I | re.M,
    )
    if uni_match:
        university = uni_match.group(1).strip()[:120]

    # Fix: take the MOST RECENT (maximum) year, not the first
    years_found = re.findall(r"\b((?:19|20)\d{2})\b", text)
    if years_found:
        int_years = [int(y) for y in years_found if 1970 <= int(y) <= 2030]
        if int_years:
            year = max(int_years)

    return {"education": degree, "university": university, "graduation_year": year}


def extract_title(text: str) -> str:
    titles = [
        "Principal Software Engineer", "Staff Software Engineer",
        "Senior Software Engineer", "Software Engineer", "Junior Software Engineer",
        "Full Stack Developer", "Frontend Developer", "Backend Developer",
        "Laravel Developer", "Python Developer", "Java Developer",
        "Mobile Developer", "iOS Developer", "Android Developer",
        "DevOps Engineer", "SRE", "Cloud Architect", "Solutions Architect",
        "Data Scientist", "Data Engineer", "ML Engineer", "AI Engineer",
        "Product Manager", "Product Owner", "Scrum Master", "Agile Coach",
        "UX Designer", "UI Designer", "Graphic Designer",
        "HR Manager", "Recruiter", "Talent Acquisition", "HR Executive",
        "Project Manager", "Program Manager", "Technical Lead", "Team Lead",
        "CTO", "VP Engineering", "Engineering Manager", "Director",
        "Accountant", "Senior Accountant", "Staff Accountant", "CPA",
        "Financial Analyst", "Bookkeeper", "Controller", "Finance Manager",
        "Business Analyst", "Consultant", "Architect", "Developer",
    ]
    lower = text.lower()
    for title in titles:
        if title.lower() in lower:
            return title
    return ""


def extract_summary(text: str) -> str:
    summary_match = re.search(
        r"(?:summary|profile|about\s+me|professional\s+summary|objective)\s*[:\-]?\s*(.+?)(?:\n\n|\n[A-Z]{2,}|\Z)",
        text,
        re.I | re.S,
    )
    if summary_match:
        return summary_match.group(1).strip()[:500]

    paragraphs = [p.strip() for p in re.split(r"\n\s*\n", text) if len(p.strip()) > 40]
    for para in paragraphs:
        if "@" not in para and not re.match(r"^(skills|experience|education)", para, re.I):
            return para[:500]
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


def try_pyresparser(path: Path, text: str):
    try:
        import spacy  # noqa: F401
        from pyresparser import ResumeParser  # type: ignore

        data = ResumeParser(str(path)).get_extracted_data()
        if not data:
            return None
        name = data.get("name") or ""
        email = (data.get("email") or "").strip()
        if isinstance(email, str) and email.startswith("["):
            email = ""
        skills = data.get("skills") or []
        if isinstance(skills, str):
            skills = [s.strip() for s in skills.split(",") if s.strip()]
        return {
            "name": name,
            "email": email,
            "phone": (data.get("mobile_number") or [""])[0] if isinstance(data.get("mobile_number"), list) else str(data.get("mobile_number") or ""),
            "title": extract_title(text),
            "skills": skills[:15] if skills else extract_skills(text),
        }
    except Exception:
        return None


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
    summary = extract_summary(text)
    score = ai_score(skills, years)

    name = extract_name(text)
    email = extract_email(text)
    phone = extract_phone(text)
    title = extract_title(text)
    location = extract_location(text)
    previous_companies = extract_previous_companies(text)

    enhanced = try_pyresparser(path, text)
    if enhanced:
        name = enhanced.get("name") or name
        email = enhanced.get("email") or email
        phone = enhanced.get("phone") or phone
        title = enhanced.get("title") or title
        if enhanced.get("skills"):
            skills = enhanced["skills"]

    recommendation = (
        "Strong profile with solid technical breadth. Recommended for senior engineering and product roles."
        if score >= 85
        else "Good foundational profile. Consider roles aligned with core skills and experience level."
    )

    payload = {
        "name": name or "Unknown Candidate",
        "email": email,
        "phone": phone,
        "location": location,
        "title": title,
        "current_title": title,
        "skills": skills,
        "experience": f"{years} Years" if years else "",
        "experience_years": years,
        "education": edu.get("education") or "",
        "university": edu.get("university") or "",
        "graduation_year": edu.get("graduation_year"),
        "seniority_level": seniority_from_years(years),
        "previous_companies": previous_companies,
        "summary": summary or recommendation,
        "ai_score": score,
        "ai_recommendation": summary or recommendation,
        "skill_accuracy": min(98, 85 + len(skills)),
        "confidence_score": min(99, 70 + len(skills) * 2 + min(years * 2, 14) + (5 if location else 0)),
    }
    print(json.dumps(payload))


if __name__ == "__main__":
    main()
