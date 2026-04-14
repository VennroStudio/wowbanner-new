import { useEffect, useState } from 'react';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useQueryClient } from '@tanstack/react-query';
import {
  useCreateProcessingCommand,
  useUpdateProcessingCommand,
  useProcessingQuery,
  useProcessingTypesQuery,
  processingApi,
} from '@/entities/processing';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ModalDialog, RichTextEditor } from '@/shared/ui';
import { fieldInputClass, fieldSelectClass } from '@/shared/ui';
import {
  processingFormSchema,
  getProcessingFormDefaultValues,
  type ProcessingFormValues,
} from './lib/processingFormSchema';
import {
  mapProcessingToFormValues,
  buildCreateProcessingBody,
  buildUpdateProcessingBody,
} from './lib/processingFormMappers';
import { getDirtyProcessingImageAltUpdates } from './lib/getDirtyProcessingImageAltUpdates';
import { ProcessingFormFooter } from './ui/ProcessingFormFooter';
import { ProcessingImagesSection } from './ui/ProcessingImagesSection';

interface ProcessingFormModalProps {
  open: boolean;
  mode: 'create' | 'edit';
  processingId?: number;
  onClose: () => void;
  onSuccess?: (mode: 'create' | 'edit') => void;
}

export const ProcessingFormModal = ({
  open,
  mode,
  processingId,
  onClose,
  onSuccess,
}: ProcessingFormModalProps) => {
  const queryClient = useQueryClient();
  const createMutation = useCreateProcessingCommand();
  const updateMutation = useUpdateProcessingCommand();
  const { data: processingResponse, isLoading: isLoadingProcessing } = useProcessingQuery(
    processingId ?? 0,
    {
      enabled: open && mode === 'edit' && processingId != null && processingId > 0,
    },
  );
  const { data: processingTypes = [] } = useProcessingTypesQuery({
    enabled: open,
  });

  const {
    register,
    control,
    handleSubmit,
    reset,
    setValue,
    getValues,
    formState: { errors },
  } = useForm<ProcessingFormValues>({
    resolver: zodResolver(processingFormSchema),
    defaultValues: getProcessingFormDefaultValues(),
  });

  const [altDrafts, setAltDrafts] = useState<Record<number, string>>({});
  const [isSavingAlts, setIsSavingAlts] = useState(false);

  useEffect(() => {
    if (!open) return;
    setAltDrafts({});
  }, [open, processingId]);

  useEffect(() => {
    if (!open) return;
    if (mode === 'create') {
      reset(getProcessingFormDefaultValues());
    }
  }, [open, mode, reset]);

  useEffect(() => {
    if (!open || mode !== 'edit') return;
    const inner = processingResponse?.data;
    if (inner) {
      reset(mapProcessingToFormValues(inner));
    }
  }, [open, mode, processingResponse, reset]);

  useEffect(() => {
    if (!open || mode !== 'create' || processingTypes.length === 0) return;
    const cur = getValues('typeId');
    if (!cur || cur === 0) {
      setValue('typeId', processingTypes[0].id);
    }
  }, [open, mode, processingTypes, getValues, setValue]);

  const [submitError, setSubmitError] = useState<string | null>(null);

  useEffect(() => {
    if (open) setSubmitError(null);
  }, [open]);

  const onSubmit = async (values: ProcessingFormValues) => {
    setSubmitError(null);
    try {
      if (mode === 'create') {
        await createMutation.mutateAsync(buildCreateProcessingBody(values));
        setAltDrafts({});
        onSuccess?.(mode);
        onClose();
        return;
      }

      if (processingId == null) return;

      await updateMutation.mutateAsync({
        id: processingId,
        body: buildUpdateProcessingBody(values),
      });

      const images = processingResponse?.data?.images ?? [];
      const dirty = getDirtyProcessingImageAltUpdates(images, altDrafts);

      if (dirty.length > 0) {
        setIsSavingAlts(true);
        try {
          await Promise.all(
            dirty.map(({ imageId, alt }) => processingApi.updateImageAlt(imageId, alt)),
          );
        } catch (e) {
          setSubmitError(
            `Обработка сохранена, но подписи к изображениям не обновились: ${getApiErrorMessage(e)}`,
          );
          await queryClient.invalidateQueries({ queryKey: ['processing', processingId] });
          await queryClient.invalidateQueries({ queryKey: ['processings'] });
          setAltDrafts({});
          return;
        } finally {
          setIsSavingAlts(false);
        }
        await queryClient.invalidateQueries({ queryKey: ['processing', processingId] });
        await queryClient.invalidateQueries({ queryKey: ['processings'] });
      }

      setAltDrafts({});
      onSuccess?.(mode);
      onClose();
    } catch (e) {
      setSubmitError(getApiErrorMessage(e));
    }
  };

  const isPending =
    createMutation.isPending || updateMutation.isPending || isSavingAlts;
  const showLoader = mode === 'edit' && isLoadingProcessing;
  const title = mode === 'create' ? 'Новая обработка' : 'Редактирование обработки';
  const processing = processingResponse?.data;

  return (
    <ModalDialog
      open={open}
      title={title}
      titleId="processing-form-title"
      onClose={onClose}
      size="2xl"
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
              <label className="block text-xs font-medium text-slate-500 mb-1">Название *</label>
              <input
                {...register('name')}
                className={fieldInputClass}
                autoComplete="off"
                autoFocus
                disabled={isPending}
              />
              {errors.name?.message && (
                <p className="text-xs text-red-600 mt-1">{String(errors.name.message)}</p>
              )}
            </div>

            <div>
              <label className="block text-xs font-medium text-slate-500 mb-1">Тип расчёта *</label>
              <select {...register('typeId', { valueAsNumber: true })} className={fieldSelectClass} disabled={isPending}>
                <option value={0}>Выберите тип…</option>
                {processingTypes.map((t) => (
                  <option key={t.id} value={t.id}>
                    {t.label}
                  </option>
                ))}
              </select>
              {errors.typeId?.message && (
                <p className="text-xs text-red-600 mt-1">{String(errors.typeId.message)}</p>
              )}
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-medium text-slate-500 mb-1">Себестоимость *</label>
                <input
                  {...register('costPrice')}
                  className={fieldInputClass}
                  autoComplete="off"
                  inputMode="decimal"
                  disabled={isPending}
                />
                {errors.costPrice?.message && (
                  <p className="text-xs text-red-600 mt-1">{String(errors.costPrice.message)}</p>
                )}
              </div>
              <div>
                <label className="block text-xs font-medium text-slate-500 mb-1">Цена *</label>
                <input
                  {...register('price')}
                  className={fieldInputClass}
                  autoComplete="off"
                  inputMode="decimal"
                  disabled={isPending}
                />
                {errors.price?.message && (
                  <p className="text-xs text-red-600 mt-1">{String(errors.price.message)}</p>
                )}
              </div>
            </div>

            <div>
              <label className="block text-xs font-medium text-slate-500 mb-1">Описание</label>
              <Controller
                name="description"
                control={control}
                render={({ field }) => (
                  <RichTextEditor
                    value={field.value ?? ''}
                    onChange={field.onChange}
                    placeholder="Описание обработки"
                    disabled={isPending}
                  />
                )}
              />
              {errors.description?.message && (
                <p className="text-xs text-red-600 mt-1">{String(errors.description.message)}</p>
              )}
            </div>

            {mode === 'edit' && processingId != null && processing && (
              <ProcessingImagesSection
                processingId={processingId}
                images={processing.images ?? []}
                altDrafts={altDrafts}
                setAltDrafts={setAltDrafts}
              />
            )}
          </div>

          <ProcessingFormFooter mode={mode} isPending={isPending} onClose={onClose} />
        </form>
      )}
    </ModalDialog>
  );
};
