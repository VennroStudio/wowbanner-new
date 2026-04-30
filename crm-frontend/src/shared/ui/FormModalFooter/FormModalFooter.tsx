interface FormModalFooterProps {
  mode: 'create' | 'edit';
  isPending: boolean;
  onClose: () => void;
  createLabel?: string;
  saveLabel?: string;
  pendingLabel?: string;
}

export const FormModalFooter = ({
  mode,
  isPending,
  onClose,
  createLabel = 'Создать',
  saveLabel = 'Сохранить',
  pendingLabel = 'Сохранение…',
}: FormModalFooterProps) => (
  <div className="flex justify-end gap-2 px-5 py-4 border-t border-slate-100 bg-slate-50/80 shrink-0">
    <button
      type="button"
      onClick={onClose}
      className="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
    >
      Отмена
    </button>
    <button
      type="submit"
      disabled={isPending}
      className="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-50"
    >
      {isPending ? pendingLabel : mode === 'create' ? createLabel : saveLabel}
    </button>
  </div>
);
