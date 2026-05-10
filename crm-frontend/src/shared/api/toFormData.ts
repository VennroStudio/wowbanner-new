const appendFormValue = (formData: FormData, key: string, value: unknown): void => {
  if (value == null) {
    return;
  }

  if (value instanceof File) {
    formData.append(key, value);
    return;
  }

  if (Array.isArray(value)) {
    value.forEach((item, index) => {
      if (item instanceof File) {
        formData.append(`${key}[]`, item);
        return;
      }

      if (typeof item === 'object' && item !== null) {
        appendFormValue(formData, `${key}[${index}]`, item);
        return;
      }

      formData.append(`${key}[]`, String(item));
    });
    return;
  }

  if (typeof value === 'object') {
    Object.entries(value as Record<string, unknown>).forEach(([nestedKey, nestedValue]) => {
      appendFormValue(formData, `${key}[${nestedKey}]`, nestedValue);
    });
    return;
  }

  formData.append(key, String(value));
};

export const toFormData = <TPayload extends object>(payload: TPayload) => {
  const formData = new FormData();

  Object.entries(payload as Record<string, unknown>).forEach(([key, value]) => {
    if (value === undefined) return;
    appendFormValue(formData, key, value);
  });

  return formData;
};
