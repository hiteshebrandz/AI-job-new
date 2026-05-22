"""Extract plain text from resume files (PDF, DOCX, optional DOC)."""

import os
import re


def _clean_text(text: str) -> str:
    text = re.sub(r'\r\n', '\n', text)
    text = re.sub(r'[ \t]+', ' ', text)
    text = re.sub(r'\n{3,}', '\n\n', text)
    return text.strip()


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


def _extract_text_doc(path: str) -> str:
    try:
        import textract  # type: ignore
        raw = textract.process(path)
        if isinstance(raw, bytes):
            return raw.decode('utf-8', errors='replace')
        return str(raw)
    except ImportError:
        raise RuntimeError(
            "Legacy .doc files require the textract package. "
            "Please save your resume as .docx or .pdf and upload again."
        )
    except Exception as exc:
        raise RuntimeError(f"Could not read .doc file: {exc}") from exc


def extract_resume_text(file_path: str) -> str:
    if not os.path.isfile(file_path):
        raise FileNotFoundError(f"File not found: {file_path}")

    ext = os.path.splitext(file_path)[1].lower()
    if ext == '.pdf':
        raw = _extract_text_pdf(file_path)
    elif ext == '.docx':
        raw = _extract_text_docx(file_path)
    elif ext == '.doc':
        raw = _extract_text_doc(file_path)
    else:
        raise ValueError(f"Unsupported file extension: {ext}. Use PDF, DOC, or DOCX.")

    cleaned = _clean_text(raw)
    if not cleaned:
        raise ValueError(
            "No text could be extracted from the resume. "
            "The file may be a scanned image PDF."
        )
    return cleaned
