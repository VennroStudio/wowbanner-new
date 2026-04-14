import { fieldInputClass } from '@/shared/ui';

export interface ImageAltTextInputProps {
  value: string;
  /** Новое значение поля */
  onValueChange: (value: string) => void;
  /** После ввода — сброс «Сохранено» / ошибки у этой строки */
  onEdit?: () => void;
  placeholder?: string;
  className?: string;
}

/**
 * Поле подписи (alt) для изображения: при изменении текста вызывает onEdit (сброс фидбека).
 */
export function ImageAltTextInput({
  value,
  onValueChange,
  onEdit,
  placeholder = 'Описание для доступности',
  className,
}: ImageAltTextInputProps) {
  return (
    <input
      type="text"
      value={value}
      onChange={(e) => {
        const v = e.target.value;
        onValueChange(v);
        onEdit?.();
      }}
      className={className ?? `${fieldInputClass} flex-1 min-w-[120px] text-sm py-1.5`}
      placeholder={placeholder}
    />
  );
}
