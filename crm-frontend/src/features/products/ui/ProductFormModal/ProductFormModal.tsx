import { useEffect, useMemo, useState } from 'react';
import { useForm, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
  useCreateProductCommand,
  useUpdateProductCommand,
  useProductQuery,
} from '@/entities/product';
import { useMaterialSelectQuery } from '@/entities/material';
import type { MaterialOptionSelectOption } from '@/entities/material';
import { usePrintingSelectQuery } from '@/entities/printing';
import { useModalFormState } from '@/shared/lib/useModalFormState';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { FormErrorBanner, FormModalFooter, ModalDialog } from '@/shared/ui';
import { fieldInputClass } from '@/shared/ui';
import {
  productFormSchema,
  getProductFormDefaultValues,
  type ProductFormValues,
  type ProductMaterialFormValue,
  type ProductPrintFormValue,
} from './lib/productFormSchema';
import {
  mapProductToFormValues,
  buildCreateProductBody,
  buildUpdateProductBody,
} from './lib/productFormMappers';
import { ProductMaterialsEditor } from './ui/ProductMaterialsEditor';
import { ProductPrintsEditor } from './ui/ProductPrintsEditor';

interface ProductFormModalProps {
  open: boolean;
  mode: 'create' | 'edit';
  productId?: number;
  onClose: () => void;
  onSuccess?: (mode: 'create' | 'edit') => void;
}

const areMaterialOptionsEqual = (
  left: MaterialOptionSelectOption[] | undefined,
  right: MaterialOptionSelectOption[],
) => {
  if (!left) return false;
  if (left.length !== right.length) return false;

  return left.every((item, index) => {
    const pair = right[index];
    return pair != null && pair.id === item.id && pair.name === item.name;
  });
};

export const ProductFormModal = ({
  open,
  mode,
  productId,
  onClose,
  onSuccess,
}: ProductFormModalProps) => {
  const createMutation = useCreateProductCommand();
  const updateMutation = useUpdateProductCommand();
  const { data: productResponse, isLoading: isLoadingProduct } = useProductQuery(productId ?? 0, {
    enabled: open && mode === 'edit' && productId != null && productId > 0,
  });
  const { data: materialSelectOptions = [], isLoading: isLoadingMaterials } = useMaterialSelectQuery({
    enabled: open,
  });
  const { data: printingSelectOptions = [], isLoading: isLoadingPrintings } = usePrintingSelectQuery({
    enabled: open,
  });

  const {
    register,
    handleSubmit,
    reset,
    setValue,
    control,
    formState: { errors },
  } = useForm<ProductFormValues>({
    resolver: zodResolver(productFormSchema),
    defaultValues: getProductFormDefaultValues(),
  });

  const rawMaterials = useWatch({ control, name: 'materials' });
  const rawPrints = useWatch({ control, name: 'prints' });
  const materials = useMemo(() => rawMaterials ?? [], [rawMaterials]);
  const prints = useMemo(() => rawPrints ?? [], [rawPrints]);
  const [loadedMaterialOptionsById, setLoadedMaterialOptionsById] = useState<Record<number, MaterialOptionSelectOption[]>>({});

  useEffect(() => {
    if (!open) return;
    if (mode === 'create') {
      reset(getProductFormDefaultValues());
    }
  }, [open, mode, reset]);

  useEffect(() => {
    if (!open || mode !== 'edit') return;
    const inner = productResponse?.data;
    if (!inner || isLoadingMaterials || isLoadingPrintings) return;
    reset(mapProductToFormValues(inner));
  }, [open, mode, productResponse, reset, isLoadingMaterials, isLoadingPrintings]);

  const prefilledMaterialOptionsById = useMemo(() => {
    const prefilled: Record<number, MaterialOptionSelectOption[]> = {};
    materials.forEach((item) => {
      if (!item.materialId || !item.materialOptionId || !item.materialOptionName) return;
      prefilled[item.materialId] = [
        ...(prefilled[item.materialId] ?? []),
        { id: item.materialOptionId, name: item.materialOptionName },
      ];
    });
    return prefilled;
  }, [materials]);

  const materialOptionsCacheNormalized = useMemo(() => {
    const normalized: Record<number, MaterialOptionSelectOption[]> = {};
    const materialIds = new Set<number>([
      ...Object.keys(loadedMaterialOptionsById).map(Number),
      ...Object.keys(prefilledMaterialOptionsById).map(Number),
    ]);

    materialIds.forEach((materialId) => {
      const options = [
        ...(loadedMaterialOptionsById[materialId] ?? []),
        ...(prefilledMaterialOptionsById[materialId] ?? []),
      ];
      const uniq = new Map<number, MaterialOptionSelectOption>();
      options.forEach((item) => uniq.set(item.id, item));
      normalized[materialId] = Array.from(uniq.values());
    });

    return normalized;
  }, [loadedMaterialOptionsById, prefilledMaterialOptionsById]);

  const { submitError, setSubmitError, handleClose } = useModalFormState({ onClose });

  const onSubmit = async (values: ProductFormValues) => {
    setSubmitError(null);
    try {
      if (mode === 'create') {
        await createMutation.mutateAsync(buildCreateProductBody(values));
      } else if (productId != null) {
        await updateMutation.mutateAsync({
          id: productId,
          body: buildUpdateProductBody(values),
        });
      }
      onSuccess?.(mode);
      handleClose();
    } catch (e) {
      setSubmitError(getApiErrorMessage(e));
    }
  };

  const appendMaterial = (item: ProductMaterialFormValue) => {
    setValue('materials', [...materials, item], { shouldDirty: true, shouldValidate: true });
  };

  const removeMaterial = (index: number) => {
    setValue(
      'materials',
      materials.filter((_, itemIndex) => itemIndex !== index),
      { shouldDirty: true, shouldValidate: true },
    );
  };

  const appendPrint = (item: ProductPrintFormValue) => {
    setValue('prints', [...prints, item], { shouldDirty: true, shouldValidate: true });
  };

  const removePrint = (index: number) => {
    setValue(
      'prints',
      prints.filter((_, itemIndex) => itemIndex !== index),
      { shouldDirty: true, shouldValidate: true },
    );
  };

  const handleMaterialOptionsCacheChange = (
    materialId: number,
    options: MaterialOptionSelectOption[],
  ) => {
    setLoadedMaterialOptionsById((current) => {
      if (areMaterialOptionsEqual(current[materialId], options)) {
        return current;
      }

      return {
        ...current,
        [materialId]: options,
      };
    });
  };

  const isPending = createMutation.isPending || updateMutation.isPending;
  const showLoader = mode === 'edit' && (isLoadingProduct || isLoadingMaterials || isLoadingPrintings);
  const title = mode === 'create' ? 'Новый продукт' : 'Редактирование продукта';

  return (
    <ModalDialog
      open={open}
      title={title}
      titleId="product-form-title"
      onClose={handleClose}
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
            <FormErrorBanner message={submitError} />

            <div>
              <label htmlFor="product-name" className="block text-xs font-medium text-slate-600 mb-1.5">
                Название
              </label>
              <input
                id="product-name"
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

            <ProductMaterialsEditor
              items={materials}
              materialOptionsById={materialOptionsCacheNormalized}
              materialSelectOptions={materialSelectOptions}
              onChangeMaterialOptionsCache={handleMaterialOptionsCacheChange}
              onAppend={appendMaterial}
              onRemove={removeMaterial}
              disabled={isPending}
            />

            <ProductPrintsEditor
              items={prints}
              printingOptions={printingSelectOptions}
              onAppend={appendPrint}
              onRemove={removePrint}
              disabled={isPending}
            />
          </div>

          <FormModalFooter mode={mode} isPending={isPending} onClose={handleClose} />
        </form>
      )}
    </ModalDialog>
  );
};
