import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
  useCreatePrintingCommand,
  useUpdatePrintingCommand,
  usePrintingQuery,
} from '@/entities/printing';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ModalDialog } from '@/shared/ui';
import { fieldInputClass } from '@/shared/ui';
import {
  printingFormSchema,
  getPrintingFormDefaultValues,
  type PrintingFormValues,
} from './lib/printingFormSchema';
import { PrintingFormFooter } from './ui/PrintingFormFooter';

interface PrintingFormModalProps {
  open: boolean;
  mode: 'create' | 'edit';
  printingId?: number;
  onClose: () => void;
  onSuccess?: (mode: 'create' | 'edit') => void;
}

export const PrintingFormModal = ({
  open,
  mode,
  printingId,
  onClose,
  onSuccess,
}: PrintingFormModalProps) => {
  const createMutation = useCreatePrintingCommand();
  const updateMutation = useUpdatePrintingCommand();
  const { data: printingResponse, isLoading: isLoadingPrinting } = usePrintingQuery(
    printingId ?? 0,
    {
      enabled: open && mode === 'edit' && printingId != null && printingId > 0,
    },
  );

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm<PrintingFormValues>({
    resolver: zodResolver(printingFormSchema),
    defaultValues: getPrintingFormDefaultValues(),
  });

  useEffect(() => {
    if (!open) return;
    if (mode === 'create') {
      reset(getPrintingFormDefaultValues());
    }
  }, [open, mode, reset]);

  useEffect(() => {
    if (!open || mode !== 'edit') return;
    const inner = printingResponse?.data;
    if (inner) {
      reset({ name: inner.name });
    }
  }, [open, mode, printingResponse, reset]);

  const [submitError, setSubmitError] = useState<string | null>(null);

  useEffect(() => {
    if (open) setSubmitError(null);
  }, [open]);

  const onSubmit = async (values: PrintingFormValues) => {
    setSubmitError(null);
    try {
      if (mode === 'create') {
        await createMutation.mutateAsync({ name: values.name.trim() });
      } else if (printingId != null) {
        await updateMutation.mutateAsync({
          id: printingId,
          body: { name: values.name.trim() },
        });
      }
      onSuccess?.(mode);
      onClose();
    } catch (e) {
      setSubmitError(getApiErrorMessage(e));
    }
  };

  const isPending = createMutation.isPending || updateMutation.isPending;
  const showLoader = mode === 'edit' && isLoadingPrinting;

  const title = mode === 'create' ? 'Новый тип печати' : 'Редактирование типа печати';

  return (
    <ModalDialog
      open={open}
      title={title}
      titleId="printing-form-title"
      onClose={onClose}
      size="md"
    >
      {showLoader ? (
        <div className="p-12 text-center text-slate-500 text-sm">Загрузка…</div>
      ) : (
        <form
          onSubmit={handleSubmit(onSubmit)}
          className="flex flex-col min-h-0 flex-1 overflow-hidden"
        >
          <div className="overflow-y-auto px-5 py-4 space-y-4">
            {submitError && (
              <div className="rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-3 py-2">
                {submitError}
              </div>
            )}

            <div>
              <label htmlFor="printing-name" className="block text-xs font-medium text-slate-600 mb-1.5">
                Название
              </label>
              <input
                id="printing-name"
                type="text"
                autoComplete="off"
                className={fieldInputClass}
                aria-invalid={errors.name ? 'true' : undefined}
                {...register('name')}
              />
              {errors.name && (
                <p className="mt-1 text-xs text-red-600">{errors.name.message}</p>
              )}
            </div>
          </div>

          <PrintingFormFooter mode={mode} isPending={isPending} onClose={onClose} />
        </form>
      )}
    </ModalDialog>
  );
};
