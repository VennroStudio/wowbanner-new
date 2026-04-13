import { useEffect, useState } from 'react';
import { useForm, useFieldArray } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
  useCreateClientCommand,
  useUpdateClientCommand,
  useClientQuery,
} from '@/entities/client';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ModalDialog } from '../ModalDialog';
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
  ClientFormFooter,
} from './ui';

interface ClientFormModalProps {
  open: boolean;
  mode: 'create' | 'edit';
  clientId?: number;
  onClose: () => void;
  onSuccess?: () => void;
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

  const {
    register,
    control,
    handleSubmit,
    reset,
    watch,
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

  const clientType = watch('type');

  useEffect(() => {
    if (!open) return;
    if (mode === 'create') {
      reset(getClientFormDefaultValues());
    }
  }, [open, mode, reset]);

  useEffect(() => {
    if (!open || mode !== 'edit') return;
    const inner = clientResponse?.data;
    if (inner) {
      reset(mapClientToFormValues(inner));
    }
  }, [open, mode, clientResponse, reset]);

  const [submitError, setSubmitError] = useState<string | null>(null);

  useEffect(() => {
    if (open) setSubmitError(null);
  }, [open]);

  const onSubmit = async (values: ClientFormValues) => {
    setSubmitError(null);
    try {
      if (mode === 'create') {
        await createMutation.mutateAsync(buildCreateClientBody(values));
      } else if (clientId != null) {
        await updateMutation.mutateAsync({ id: clientId, body: buildUpdateClientBody(values) });
      }
      onSuccess?.();
      onClose();
    } catch (e) {
      setSubmitError(getApiErrorMessage(e));
    }
  };

  const isPending = createMutation.isPending || updateMutation.isPending;
  const showLoader = mode === 'edit' && isLoadingClient;

  const title = mode === 'create' ? 'Новый клиент' : 'Редактирование клиента';

  return (
    <ModalDialog
      open={open}
      title={title}
      titleId="client-form-title"
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

          <ClientFormFooter mode={mode} isPending={isPending} onClose={onClose} />
        </form>
      )}
    </ModalDialog>
  );
};
