/** Хранение в форме/API/БД: 11 цифр, 89XXXXXXXXX (моб. РФ). */

export const RU_MOBILE_STORAGE_REGEX = /^89\d{9}$/;

export function digitsOnly(s: string): string {
  return s.replace(/\D/g, '');
}

/**
 * Ввод пользователя (только цифры по порядку) → хранение 8… (до 11 цифр).
 * 9… → ведущая 8; 79… → 8…; 8… как есть (обрезка до 11).
 */
export function parseDigitsToStorage(digits: string): string {
  const d = digitsOnly(digits);
  if (!d) return '';
  if (d.startsWith('8')) {
    return d.slice(0, 11);
  }
  if (d.startsWith('7') && d[1] === '9') {
    return ('8' + d.slice(1)).slice(0, 11);
  }
  if (d.startsWith('9')) {
    return ('8' + d.slice(0, 10)).slice(0, 11);
  }
  return '';
}

/** Национальная часть (10 цифр после 8) → отображение +7 (…) … */
export function formatBodyToDisplay(body10: string): string {
  const body = digitsOnly(body10).slice(0, 10);
  if (body.length === 0) {
    return '+7 ';
  }
  const p1 = body.slice(0, 3);
  const p2 = body.slice(3, 6);
  const p3 = body.slice(6, 8);
  const p4 = body.slice(8, 10);
  let out = '+7 (' + p1;
  if (body.length <= 3) {
    return body.length === 3 ? `${out}) ` : out;
  }
  out += ') ' + p2;
  if (body.length <= 6) {
    return out;
  }
  out += '-' + p3;
  if (body.length <= 8) {
    return out;
  }
  return `${out}-${p4}`;
}

/** Хранение 89… (частичное или полное) → строка для input */
export function storageToDisplay(storage: string): string {
  const d = digitsOnly(storage);
  if (!d) return '';
  if (d.startsWith('8')) {
    return formatBodyToDisplay(d.slice(1));
  }
  if (d.startsWith('9')) {
    return formatBodyToDisplay(d.slice(0, 10));
  }
  return '';
}

export function isValidRuMobileStorage(storage: string): boolean {
  return RU_MOBILE_STORAGE_REGEX.test(digitsOnly(storage));
}

/** Таблица / read-only: из значения БД */
export function formatRuMobileDisplayFromStorage(storage: string): string {
  const s = storageToDisplay(storage);
  return s || storage;
}
