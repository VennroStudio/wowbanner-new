import { useEffect } from 'react';
import { useForm, useFieldArray, useWatch } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
  useCreateClientCommand,
  useUpdateClientCommand,
  useClientQuery,
  useClientTypesQuery,
  useClientDocsTypesQuery,
  useClientPhoneTypesQuery,
} from '@/entities/client';
import { useModalFormState } from '@/shared/lib/useModalFormState';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { FormErrorBanner, FormModalFooter, ModalDialog } from '@/shared/ui';
import {
  clientFormSchema,
  getClientFormDefaultValues,
  type ClientFormValues,
} from './lib/clientFormSchema';
import {
  mapClientToFormValues,
  buildCreateClientBody,
  buildUpdateClientBody,
} from './lib/clientFormMappers';
import {
  ClientFormIdentityFields,
  ClientFormTypeDocsFields,
  ClientFormInfoField,
  ClientPhonesEditor,
  ClientCompaniesEditor,
} from './ui';

interface ClientFormModalProps {
  open: boolean;
  mode: 'create' | 'edit';
  clientId?: number;
  onClose: () => void;
  /** Вызывается после успешного create/update; режим не зависит от состояния страницы-родителя */
  onSuccess?: (mode: 'create' | 'edit') => void;
}

export const ClientFormModal = ({
  open,
  mode,
  clientId,
  onClose,
  onSuccess,
}: ClientFormModalProps) => {
  const createMutation = useCreateClientCommand();
  const updateMutation = useUpdateClientCommand();
  const { data: clientResponse, isLoading: isLoadingClient } = useClientQuery(clientId ?? 0, {
    enabled: open && mode === 'edit' && clientId != null && clientId > 0,
  });

  const { isLoading: isLoadingClientTypes } = useClientTypesQuery({
    enabled: open,
  });
  const { isLoading: isLoadingClientDocsTypes } = useClientDocsTypesQuery({
    enabled: open,
  });
  const { isLoading: isLoadingClientPhoneTypes } = useClientPhoneTypesQuery({
    enabled: open,
  });

  const isDictsLoading =
    isLoadingClientTypes || isLoadingClientDocsTypes || isLoadingClientPhoneTypes;

  const {
    register,
    control,
    handleSubmit,
    reset,
    getValues,
    formState: { errors },
  } = useForm<ClientFormValues>({
    resolver: zodResolver(clientFormSchema),
    defaultValues: getClientFormDefaultValues(),
  });

  const { fields: phoneFields, append: appendPhone, remove: removePhone } = useFieldArray({
    control,
    name: 'phones',
    keyName: 'fieldId',
  });

  const {
    fields: companyFields,
    append: appendCompany,
    remove: removeCompany,
    replace: replaceCompanies,
  } = useFieldArray({
    control,
    name: 'companies',
    keyName: 'fieldId',
  });

  const clientType = useWatch({
    control,
    name: 'type',
  });

  useEffect(() => {
    if (!open) return;
    if (mode === 'create') {
      reset(getClientFormDefaultValues());
    }
  }, [open, mode, reset]);

  useEffect(() => {
    if (!open || mode !== 'edit') return;
    const inner = clientResponse?.data;
    if (!inner || isDictsLoading) return;
    reset(mapClientToFormValues(inner));
  }, [open, mode, clientResponse, reset, isDictsLoading]);

  const { submitError, setSubmitError, handleClose } = useModalFormState({ onClose });

  const onSubmit = async (values: ClientFormValues) => {
    setSubmitError(null);
    try {
      if (mode === 'create') {
        await createMutation.mutateAsync(buildCreateClientBody(values));
      } else if (clientId != null) {
        await updateMutation.mutateAsync({ id: clientId, body: buildUpdateClientBody(values) });
      }
      onSuccess?.(mode);
      handleClose();
    } catch (e) {
      setSubmitError(getApiErrorMessage(e));
    }
  };

  const isPending = createMutation.isPending || updateMutation.isPending;
  const showLoader = mode === 'edit' && (isLoadingClient || isDictsLoading);

  const title = mode === 'create' ? 'Новый клиент' : 'Редактирование клиента';

  return (
    <ModalDialog
      open={open}
      title={title}
      titleId="client-form-title"
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

            <ClientFormIdentityFields register={register} errors={errors} />

            <ClientFormTypeDocsFields
              register={register}
              getValues={getValues}
              appendCompany={appendCompany}
              replaceCompanies={replaceCompanies}
            />

            <ClientFormInfoField register={register} />

            <ClientPhonesEditor
              phoneFields={phoneFields}
              register={register}
              control={control}
              errors={errors}
              appendPhone={appendPhone}
              removePhone={removePhone}
            />

            {clientType === 2 && (
              <ClientCompaniesEditor
                companyFields={companyFields}
                register={register}
                appendCompany={appendCompany}
                removeCompany={removeCompany}
                errors={errors}
              />
            )}
          </div>

          <FormModalFooter mode={mode} isPending={isPending} onClose={handleClose} />
        </form>
      )}
    </ModalDialog>
  );
};
