/**
 * Убирает HTML для превью в списках (без XSS через innerHTML).
 */
export function htmlToPlainText(html: string): string {
  if (!html.trim()) return '';
  if (typeof document === 'undefined') {
    return html.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
  }
  const doc = new DOMParser().parseFromString(html, 'text/html');
  return (doc.body.textContent || '').replace(/\s+/g, ' ').trim();
}
