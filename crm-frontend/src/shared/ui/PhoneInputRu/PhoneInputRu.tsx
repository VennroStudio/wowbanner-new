import { forwardRef } from 'react';
import {
  digitsOnly,
  parseDigitsToStorage,
  storageToDisplay,
} from '@/shared/lib/ruMobilePhone';

interface PhoneInputRuProps {
  value: string;
  onChange: (storage: string) => void;
  onBlur: () => void;
  name?: string;
  id?: string;
  className?: string;
  'aria-invalid'?: boolean;
}

export const PhoneInputRu = forwardRef<HTMLInputElement, PhoneInputRuProps>(
  function PhoneInputRu(
    { value, onChange, onBlur, name, id, className, 'aria-invalid': ariaInvalid },
    ref,
  ) {
    return (
      <input
        ref={ref}
        type="tel"
        inputMode="tel"
        autoComplete="tel"
        name={name}
        id={id}
        aria-invalid={ariaInvalid}
        value={storageToDisplay(value)}
        placeholder="+7 (___) ___-__-__"
        onChange={(e) => {
          const storage = parseDigitsToStorage(digitsOnly(e.target.value));
          onChange(storage);
        }}
        onBlur={onBlur}
        className={className}
      />
    );
  },
);
