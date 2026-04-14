import { useEffect, useState } from 'react';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useQueryClient } from '@tanstack/react-query';
import {
  useCreateMaterialCommand,
  useUpdateMaterialCommand,
  useMaterialQuery,
  materialApi,
} from '@/entities/material';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ModalDialog, RichTextEditor } from '@/shared/ui';
import { fieldInputClass } from '@/shared/ui';
import {
  materialFormSchema,
  getMaterialFormDefaultValues,
  type MaterialFormValues,
} from './lib/materialFormSchema';
import {
  mapMaterialToFormValues,
  buildCreateMaterialBody,
  buildUpdateMaterialBody,
} from './lib/materialFormMappers';
import { getDirtyImageAltUpdates } from './lib/getDirtyImageAltUpdates';
import { MaterialFormFooter } from './ui/MaterialFormFooter';
import { MaterialImagesSection } from './ui/MaterialImagesSection';

interface MaterialFormModalProps {
  open: boolean;
  mode: 'create' | 'edit';
  materialId?: number;
  onClose: () => void;
  onSuccess?: (mode: 'create' | 'edit') => void;
}

export const MaterialFormModal = ({
  open,
  mode,
  materialId,
  onClose,
  onSuccess,
}: MaterialFormModalProps) => {
  const queryClient = useQueryClient();
  const createMutation = useCreateMaterialCommand();
  const updateMutation = useUpdateMaterialCommand();
  const { data: materialResponse, isLoading: isLoadingMaterial } = useMaterialQuery(materialId ?? 0, {
    enabled: open && mode === 'edit' && materialId != null && materialId > 0,
  });

  const {
    register,
    control,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm<MaterialFormValues>({
    resolver: zodResolver(materialFormSchema),
    defaultValues: getMaterialFormDefaultValues(),
  });

  const [altDrafts, setAltDrafts] = useState<Record<number, string>>({});
  const [isSavingAlts, setIsSavingAlts] = useState(false);

  useEffect(() => {
    if (!open) return;
    setAltDrafts({});
  }, [open, materialId]);

  useEffect(() => {
    if (!open) return;
    if (mode === 'create') {
      reset(getMaterialFormDefaultValues());
    }
  }, [open, mode, reset]);

  useEffect(() => {
    if (!open || mode !== 'edit') return;
    const inner = materialResponse?.data;
    if (inner) {
      reset(mapMaterialToFormValues(inner));
    }
  }, [open, mode, materialResponse, reset]);

  const [submitError, setSubmitError] = useState<string | null>(null);

  useEffect(() => {
    if (open) setSubmitError(null);
  }, [open]);

  const onSubmit = async (values: MaterialFormValues) => {
    setSubmitError(null);
    try {
      if (mode === 'create') {
        await createMutation.mutateAsync(buildCreateMaterialBody(values));
        setAltDrafts({});
        onSuccess?.(mode);
        onClose();
        return;
      }

      if (materialId == null) return;

      await updateMutation.mutateAsync({
        id: materialId,
        body: buildUpdateMaterialBody(values),
      });

      const images = materialResponse?.data?.images ?? [];
      const dirty = getDirtyImageAltUpdates(images, altDrafts);

      if (dirty.length > 0) {
        setIsSavingAlts(true);
        try {
          await Promise.all(
            dirty.map(({ imageId, alt }) => materialApi.updateImageAlt(imageId, alt)),
          );
        } catch (e) {
          setSubmitError(
            `Материал сохранён, но подписи к изображениям не обновились: ${getApiErrorMessage(e)}`,
          );
          await queryClient.invalidateQueries({ queryKey: ['material', materialId] });
          await queryClient.invalidateQueries({ queryKey: ['materials'] });
          setAltDrafts({});
          return;
        } finally {
          setIsSavingAlts(false);
        }
        await queryClient.invalidateQueries({ queryKey: ['material', materialId] });
        await queryClient.invalidateQueries({ queryKey: ['materials'] });
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
  const showLoader = mode === 'edit' && isLoadingMaterial;
  const title = mode === 'create' ? 'Новый материал' : 'Редактирование материала';
  const material = materialResponse?.data;

  return (
    <ModalDialog
      open={open}
      title={title}
      titleId="material-form-title"
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
              <label className="block text-xs font-medium text-slate-500 mb-1">Описание</label>
              <Controller
                name="description"
                control={control}
                render={({ field }) => (
                  <RichTextEditor
                    value={field.value ?? ''}
                    onChange={field.onChange}
                    placeholder="Текст описания материала"
                    disabled={isPending}
                  />
                )}
              />
              {errors.description?.message && (
                <p className="text-xs text-red-600 mt-1">{String(errors.description.message)}</p>
              )}
            </div>

            {mode === 'edit' && materialId != null && material && (
              <MaterialImagesSection
                materialId={materialId}
                images={material.images ?? []}
                altDrafts={altDrafts}
                setAltDrafts={setAltDrafts}
              />
            )}
          </div>

          <MaterialFormFooter mode={mode} isPending={isPending} onClose={onClose} />
        </form>
      )}
    </ModalDialog>
  );
};
