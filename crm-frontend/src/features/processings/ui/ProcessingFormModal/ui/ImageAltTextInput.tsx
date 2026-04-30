import { fieldInputClass } from '@/shared/ui';

export interface ImageAltTextInputProps {
  value: string;
  onValueChange: (value: string) => void;
  placeholder?: string;
  className?: string;
}

export function ImageAltTextInput({
  value,
  onValueChange,
  placeholder = 'Описание для доступности',
  className,
}: ImageAltTextInputProps) {
  return (
    <input
      type="text"
      value={value}
      onChange={(e) => onValueChange(e.target.value)}
      className={className ?? `${fieldInputClass} flex-1 min-w-[120px] text-sm py-1.5`}
      placeholder={placeholder}
    />
  );
}
