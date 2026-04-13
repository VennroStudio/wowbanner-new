/** Соответствует backend enum (ClientType, Docs, PhoneType). */
export const CLIENT_TYPE_OPTIONS = [
  { value: 1, label: 'Физическое лицо' },
  { value: 2, label: 'Юридическое лицо' },
] as const;

export const DOCS_OPTIONS = [
  { value: 1, label: 'ЭДО' },
  { value: 2, label: 'Доверенность или печать' },
  { value: 3, label: 'Б/Д' },
] as const;

export const PHONE_TYPE_OPTIONS = [
  { value: 1, label: 'Основной' },
  { value: 2, label: 'Дополнительный' },
] as const;
