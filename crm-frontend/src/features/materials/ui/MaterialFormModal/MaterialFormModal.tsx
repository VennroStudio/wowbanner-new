import { useEffect, useMemo, useState } from 'react';
import { useFieldArray, useForm, Controller, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useQueryClient } from '@tanstack/react-query';
import {
  useCreateMaterialCommand,
  useUpdateMaterialCommand,
  useMaterialQuery,
  useMaterialOptionPricingTypesQuery,
  useMaterialAreaRangeTypesQuery,
  useMaterialDpiTypesQuery,
  useMaterialVariantTypesQuery,
  useMaterialPricingCutTypesQuery,
  materialApi,
  materialKeys,
} from '@/entities/material';
import { useProcessingSelectQuery } from '@/entities/processing';
import { useModalFormState } from '@/shared/lib/useModalFormState';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { FormErrorBanner, FormModalFooter, ModalDialog, RichTextEditor } from '@/shared/ui';
import { fieldInputClass } from '@/shared/ui';
import {
  materialFormSchema,
  getMaterialFormDefaultValues,
  createEmptyMaterialOption,
  type MaterialFormValues,
} from './lib/materialFormSchema';
import {
  mapMaterialToFormValues,
  buildCreateMaterialBody,
  buildUpdateMaterialBody,
} from './lib/materialFormMappers';
import { getDirtyImageAltUpdates } from './lib/getDirtyImageAltUpdates';
import { MaterialImagesSection } from './ui/MaterialImagesSection';
import { MaterialOptionTabs } from './ui/MaterialOptionTabs';
import { MaterialOptionEditor } from './ui/MaterialOptionEditor';

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

  const { data: optionPricingTypes = [] } = useMaterialOptionPricingTypesQuery({ enabled: open });
  const { data: areaRangeTypes = [] } = useMaterialAreaRangeTypesQuery({ enabled: open });
  const { data: dpiTypes = [] } = useMaterialDpiTypesQuery({ enabled: open });
  const { data: variantTypes = [] } = useMaterialVariantTypesQuery({ enabled: open });
  const { data: pricingCutTypes = [] } = useMaterialPricingCutTypesQuery({ enabled: open });
  const { data: processingOptions = [] } = useProcessingSelectQuery({ enabled: open });

  const {
    register,
    control,
    handleSubmit,
    reset,
    setValue,
    formState: { errors },
  } = useForm<MaterialFormValues>({
    resolver: zodResolver(materialFormSchema),
    defaultValues: getMaterialFormDefaultValues(),
  });

  const { fields: optionFields, append, remove } = useFieldArray({
    control,
    name: 'options',
    keyName: 'fieldId',
  });

  const watchedOptions = useWatch({
    control,
    name: 'options',
  });

  const [altDrafts, setAltDrafts] = useState<Record<number, string>>({});
  const [isSavingAlts, setIsSavingAlts] = useState(false);
  const [activeTab, setActiveTab] = useState<'base' | number>('base');

  const defaultPricingTypeId = optionPricingTypes[0]?.id ?? 0;

  useEffect(() => {
    if (!open) return;
    setAltDrafts({});
    setActiveTab('base');
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

  useEffect(() => {
    if (activeTab === 'base') return;
    if (optionFields.length === 0) {
      setActiveTab('base');
      return;
    }
    if (activeTab >= optionFields.length) {
      setActiveTab(optionFields.length - 1);
    }
  }, [activeTab, optionFields.length]);

  const { submitError, setSubmitError, handleClose } = useModalFormState({
    onClose,
    onReset: () => setAltDrafts({}),
  });

  const optionLabels = useMemo(
    () => (watchedOptions ?? []).map((option, index) => option?.name?.trim() || `Опция ${index + 1}`),
    [watchedOptions],
  );

  const handleAddOption = () => {
    append(createEmptyMaterialOption(defaultPricingTypeId));
    setActiveTab(optionFields.length);
  };

  const handleRemoveOption = (index: number) => {
    remove(index);
    setActiveTab((current) => {
      if (current === 'base') return current;
      if (current === index) return index > 0 ? index - 1 : 'base';
      if (current > index) return current - 1;
      return current;
    });
  };

  const onSubmit = async (values: MaterialFormValues) => {
    setSubmitError(null);
    try {
      if (mode === 'create') {
        await createMutation.mutateAsync(buildCreateMaterialBody(values));
        setAltDrafts({});
        onSuccess?.(mode);
        handleClose();
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
          await queryClient.invalidateQueries({ queryKey: materialKeys.detail(materialId) });
          await queryClient.invalidateQueries({ queryKey: materialKeys.lists() });
          setAltDrafts({});
          return;
        } finally {
          setIsSavingAlts(false);
        }
        await queryClient.invalidateQueries({ queryKey: materialKeys.detail(materialId) });
        await queryClient.invalidateQueries({ queryKey: materialKeys.lists() });
      }

      setAltDrafts({});
      onSuccess?.(mode);
      handleClose();
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
      onClose={handleClose}
      size="4xl"
    >
      {showLoader ? (
        <div className="p-12 text-center text-slate-500 text-sm">Загрузка…</div>
      ) : (
        <form
          onSubmit={handleSubmit(onSubmit)}
          className="flex flex-col min-h-0 flex-1 overflow-hidden"
        >
          <MaterialOptionTabs
            optionLabels={optionLabels}
            activeTab={activeTab}
            onSelect={setActiveTab}
            onAdd={handleAddOption}
            onRemove={handleRemoveOption}
            disabled={isPending}
          />

          <div className="overflow-y-auto px-5 py-4 space-y-4">
            <FormErrorBanner message={submitError} />

            {activeTab === 'base' ? (
              <>
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
              </>
            ) : (
              <MaterialOptionEditor
                optionIndex={activeTab}
                control={control}
                register={register}
                setValue={setValue}
                errors={errors}
                optionPricingTypes={optionPricingTypes}
                areaRangeTypes={areaRangeTypes}
                dpiTypes={dpiTypes}
                variantTypes={variantTypes}
                pricingCutTypes={pricingCutTypes}
                processingOptions={processingOptions}
                disabled={isPending}
              />
            )}
          </div>

          <FormModalFooter mode={mode} isPending={isPending} onClose={handleClose} />
        </form>
      )}
    </ModalDialog>
  );
};
