#!/usr/bin/env python3
"""
Extract plain text from PDF, DOCX, or TXT.
Usage: python3 extract_document_text.py <file_path>
Prints: {"success": true, "text": "..."} or {"success": false, "error": "..."}
"""
import json
import re
import sys
import os


def _error(message: str) -> None:
    print(json.dumps({"success": False, "error": message}))
    sys.exit(1)


def _extract_text_pdf(path: str) -> str:
    try:
        import fitz
        parts = []
        with fitz.open(path) as doc:
            for page in doc:
                parts.append(page.get_text())
        return "\n".join(parts)
    except ImportError:
        pass
    try:
        import pdfplumber
        parts = []
        with pdfplumber.open(path) as pdf:
            for page in pdf.pages:
                parts.append(page.extract_text() or "")
        return "\n".join(parts)
    except ImportError:
        pass
    try:
        from pypdf import PdfReader
        reader = PdfReader(path)
        return "\n".join((page.extract_text() or "") for page in reader.pages)
    except Exception as exc:
        _error(f"PDF extraction failed: {exc}")


def _extract_text_docx(path: str) -> str:
    try:
        from docx import Document
        doc = Document(path)
        return "\n".join(para.text for para in doc.paragraphs)
    except Exception as exc:
        _error(f"DOCX extraction failed: {exc}")


def _clean(text: str) -> str:
    text = re.sub(r'\r\n', '\n', text)
    text = re.sub(r'[ \t]+', ' ', text)
    text = re.sub(r'\n{3,}', '\n\n', text)
    return text.strip()


def main():
    if len(sys.argv) < 2:
        _error("Usage: extract_document_text.py <file_path>")

    path = sys.argv[1]
    if not os.path.isfile(path):
        _error(f"File not found: {path}")

    ext = os.path.splitext(path)[1].lower()
    if ext == '.pdf':
        raw = _extract_text_pdf(path)
    elif ext == '.docx':
        raw = _extract_text_docx(path)
    elif ext in ('.txt', ''):
        with open(path, 'r', encoding='utf-8', errors='replace') as f:
            raw = f.read()
    else:
        _error(f"Unsupported extension: {ext}")

    text = _clean(raw)
    if not text:
        _error("No text could be extracted from the file.")

    print(json.dumps({"success": True, "text": text}))


if __name__ == '__main__':
    main()
