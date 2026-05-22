"""Export structured resume content to PDF using ReportLab."""

from reportlab.lib import colors
from reportlab.lib.enums import TA_LEFT
from reportlab.lib.pagesizes import letter
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import inch
from reportlab.platypus import ListFlowable, ListItem, Paragraph, SimpleDocTemplate, Spacer


def _styles():
    base = getSampleStyleSheet()
    return {
        'name': ParagraphStyle(
            'Name',
            parent=base['Heading1'],
            fontSize=18,
            spaceAfter=4,
            textColor=colors.HexColor('#1a1a2e'),
        ),
        'contact': ParagraphStyle(
            'Contact',
            parent=base['Normal'],
            fontSize=9,
            textColor=colors.HexColor('#444444'),
            spaceAfter=12,
        ),
        'section': ParagraphStyle(
            'Section',
            parent=base['Heading2'],
            fontSize=11,
            spaceBefore=10,
            spaceAfter=6,
            textColor=colors.HexColor('#4f46e5'),
            borderPadding=0,
        ),
        'body': ParagraphStyle(
            'Body',
            parent=base['Normal'],
            fontSize=10,
            leading=14,
            alignment=TA_LEFT,
        ),
        'bullet': ParagraphStyle(
            'Bullet',
            parent=base['Normal'],
            fontSize=10,
            leading=13,
            leftIndent=12,
        ),
        'role': ParagraphStyle(
            'Role',
            parent=base['Normal'],
            fontSize=10,
            leading=12,
            spaceBefore=4,
            fontName='Helvetica-Bold',
        ),
    }


def _escape(text: str) -> str:
    if not text:
        return ''
    return (
        str(text)
        .replace('&', '&amp;')
        .replace('<', '&lt;')
        .replace('>', '&gt;')
    )


def export_resume_to_pdf(content: dict, output_path: str) -> None:
    styles = _styles()
    story = []

    contact = content.get('contact') or {}
    name = contact.get('name') or 'Resume'
    story.append(Paragraph(_escape(name), styles['name']))

    contact_parts = [
        contact.get('email'),
        contact.get('phone'),
        contact.get('location'),
        contact.get('linkedin'),
    ]
    contact_line = ' | '.join(_escape(p) for p in contact_parts if p)
    if contact_line:
        story.append(Paragraph(contact_line, styles['contact']))

    summary = content.get('summary') or ''
    if summary:
        story.append(Paragraph('PROFESSIONAL SUMMARY', styles['section']))
        story.append(Paragraph(_escape(summary), styles['body']))
        story.append(Spacer(1, 0.1 * inch))

    experience = content.get('experience') or []
    if experience:
        story.append(Paragraph('EXPERIENCE', styles['section']))
        for job in experience:
            title = job.get('title') or ''
            company = job.get('company') or ''
            dates = job.get('dates') or ''
            header = ' — '.join(p for p in [title, company, dates] if p)
            if header:
                story.append(Paragraph(_escape(header), styles['role']))
            bullets = job.get('bullets') or []
            if bullets:
                items = [
                    ListItem(Paragraph(_escape(b), styles['bullet']), leftIndent=12)
                    for b in bullets if b
                ]
                if items:
                    story.append(ListFlowable(items, bulletType='bullet', start='•'))

    education = content.get('education') or []
    if education:
        story.append(Paragraph('EDUCATION', styles['section']))
        for edu in education:
            line = ' — '.join(
                p for p in [
                    edu.get('degree'),
                    edu.get('institution'),
                    edu.get('dates'),
                ] if p
            )
            if line:
                story.append(Paragraph(_escape(line), styles['body']))
            details = edu.get('details')
            if details:
                story.append(Paragraph(_escape(details), styles['body']))

    skills = content.get('skills') or []
    if skills:
        story.append(Paragraph('SKILLS', styles['section']))
        if isinstance(skills, list):
            skill_text = ', '.join(_escape(str(s)) for s in skills if s)
        else:
            skill_text = _escape(str(skills))
        story.append(Paragraph(skill_text, styles['body']))

    projects = content.get('projects') or []
    if projects:
        story.append(Paragraph('PROJECTS', styles['section']))
        for proj in projects:
            pname = proj.get('name') or ''
            if pname:
                story.append(Paragraph(_escape(pname), styles['role']))
            desc = proj.get('description')
            if desc:
                story.append(Paragraph(_escape(desc), styles['body']))
            bullets = proj.get('bullets') or []
            if bullets:
                items = [
                    ListItem(Paragraph(_escape(b), styles['bullet']), leftIndent=12)
                    for b in bullets if b
                ]
                if items:
                    story.append(ListFlowable(items, bulletType='bullet', start='•'))

    doc = SimpleDocTemplate(
        output_path,
        pagesize=letter,
        rightMargin=0.75 * inch,
        leftMargin=0.75 * inch,
        topMargin=0.75 * inch,
        bottomMargin=0.75 * inch,
    )
    doc.build(story)
