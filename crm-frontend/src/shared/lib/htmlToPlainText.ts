/**
 * Превращает HTML в обычный текст для превью в таблице (без innerHTML).
 * `textContent` и часто `innerText` у фрагмента из DOMParser склеивают
 * соседние блоки без разделителя — поэтому обходим дерево и после каждого
 * блочного узла добавляем перенос строки.
 */

const BLOCK_TAGS = new Set([
  'ADDRESS',
  'ARTICLE',
  'ASIDE',
  'BLOCKQUOTE',
  'CAPTION',
  'DD',
  'DIV',
  'DL',
  'DT',
  'FIGCAPTION',
  'FIGURE',
  'FOOTER',
  'FORM',
  'H1',
  'H2',
  'H3',
  'H4',
  'H5',
  'H6',
  'HEADER',
  'HR',
  'LI',
  'MAIN',
  'NAV',
  'OL',
  'P',
  'PRE',
  'SECTION',
  'TABLE',
  'TBODY',
  'TD',
  'TFOOT',
  'TH',
  'THEAD',
  'TR',
  'UL',
]);

function isBlockTag(tag: string): boolean {
  return BLOCK_TAGS.has(tag);
}

function walkNode(node: Node): string {
  if (node.nodeType === Node.TEXT_NODE) {
    return (node.textContent || '').replace(/\s+/g, ' ');
  }

  if (node.nodeType !== Node.ELEMENT_NODE) {
    return '';
  }

  const el = node as Element;
  const tag = el.tagName;

  if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'NOSCRIPT') {
    return '';
  }

  if (tag === 'BR') {
    return '\n';
  }

  if (tag === 'HR') {
    return '\n';
  }

  let inner = '';
  for (let i = 0; i < el.childNodes.length; i++) {
    inner += walkNode(el.childNodes[i]);
  }

  if (isBlockTag(tag)) {
    const t = inner.trim();
    return t ? `${t}\n` : '';
  }

  return inner;
}

function normalizePlainText(s: string): string {
  return s
    .replace(/\u00a0/g, ' ')
    .split('\n')
    .map((line) => line.replace(/[ \t\f\v]+/g, ' ').trimEnd())
    .join('\n')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
}

export function htmlToPlainText(html: string): string {
  const raw = html.trim();
  if (!raw) return '';

  if (typeof document === 'undefined') {
    return normalizePlainText(stripHtmlFallback(raw));
  }

  const doc = new DOMParser().parseFromString(raw, 'text/html');
  const body = doc.body;

  let out = '';
  for (let i = 0; i < body.childNodes.length; i++) {
    out += walkNode(body.childNodes[i]);
  }

  return normalizePlainText(out);
}

/** Без DOM: вставляем переносы после закрывающих блочных тегов */
function stripHtmlFallback(html: string): string {
  return html
    .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
    .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
    .replace(/<br\s*\/?>/gi, '\n')
    .replace(
      /<\/(p|div|h[1-6]|li|tr|td|th|blockquote|table|thead|tbody|tfoot|ul|ol|figure|figcaption)>/gi,
      '\n',
    )
    .replace(/<[^>]+>/g, ' ')
    .replace(/[ \t]+/g, ' ')
    .replace(/\n[ \t]+/g, '\n')
    .replace(/[ \t]+\n/g, '\n')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
}
