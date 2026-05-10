import type { ClientEnumOption } from '@/entities/client';
import type { MaterialSelectOption, MaterialOptionSelectOption } from '@/entities/material';
import type { OrderEnumRef } from '@/entities/order';
import type { PrintingSelectOption } from '@/entities/printing';
import { fieldInputClass, fieldSelectClass } from '@/shared/ui';

export interface OrdersFilterValues {
  dateFrom: string;
  dateTo: string;
  printId: string;
  materialId: string;
  optionId: string;
  docs: string;
  managerId: string;
  designerId: string;
  statusType: string;
  storageType: string;
  serviceType: string;
  archived: boolean;
  deleted: boolean;
}

interface OrdersFiltersProps {
  values: OrdersFilterValues;
  printingOptions: PrintingSelectOption[];
  materialOptions: MaterialSelectOption[];
  materialOptionOptions: MaterialOptionSelectOption[];
  docsOptions: ClientEnumOption[];
  statusOptions: OrderEnumRef[];
  storageOptions: OrderEnumRef[];
  serviceOptions: OrderEnumRef[];
  onChange: <K extends keyof OrdersFilterValues>(key: K, value: OrdersFilterValues[K]) => void;
  onReset: () => void;
}

const selectPlaceholderClass = 'text-slate-500';

export const OrdersFilters = ({
  values,
  printingOptions,
  materialOptions,
  materialOptionOptions,
  docsOptions,
  statusOptions,
  storageOptions,
  serviceOptions,
  onChange,
  onReset,
}: OrdersFiltersProps) => (
  <div className="mb-6 rounded-xl border border-slate-200 bg-white p-4">
    <div className="mb-4 flex items-center justify-between gap-3">
      <div>
        <h2 className="text-sm font-semibold text-slate-900">Фильтры</h2>
        <p className="text-xs text-slate-500 mt-1">Период, документы клиента, печать, материал и служебные признаки заказа.</p>
      </div>

      <button
        type="button"
        onClick={onReset}
        className="px-3 py-2 text-sm font-medium text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer"
      >
        Сбросить
      </button>
    </div>

    <div className="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Дата от</span>
        <input
          type="date"
          value={values.dateFrom}
          onChange={(e) => onChange('dateFrom', e.target.value)}
          className={fieldInputClass}
        />
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Дата до</span>
        <input
          type="date"
          value={values.dateTo}
          onChange={(e) => onChange('dateTo', e.target.value)}
          className={fieldInputClass}
        />
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Тип печати</span>
        <select
          value={values.printId}
          onChange={(e) => onChange('printId', e.target.value)}
          className={`${fieldSelectClass} ${values.printId ? '' : selectPlaceholderClass}`}
        >
          <option value="">Все</option>
          {printingOptions.map((option) => (
            <option key={option.id} value={option.id}>
              {option.name}
            </option>
          ))}
        </select>
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Материал</span>
        <select
          value={values.materialId}
          onChange={(e) => onChange('materialId', e.target.value)}
          className={`${fieldSelectClass} ${values.materialId ? '' : selectPlaceholderClass}`}
        >
          <option value="">Все</option>
          {materialOptions.map((option) => (
            <option key={option.id} value={option.id}>
              {option.name}
            </option>
          ))}
        </select>
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Опция материала</span>
        <select
          value={values.optionId}
          onChange={(e) => onChange('optionId', e.target.value)}
          disabled={!values.materialId}
          className={`${fieldSelectClass} ${values.optionId ? '' : selectPlaceholderClass} disabled:bg-slate-50 disabled:text-slate-400`}
        >
          <option value="">Все</option>
          {materialOptionOptions.map((option) => (
            <option key={option.id} value={option.id}>
              {option.name}
            </option>
          ))}
        </select>
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Документы клиента</span>
        <select
          value={values.docs}
          onChange={(e) => onChange('docs', e.target.value)}
          className={`${fieldSelectClass} ${values.docs ? '' : selectPlaceholderClass}`}
        >
          <option value="">Все</option>
          {docsOptions.map((option) => (
            <option key={option.id} value={option.id}>
              {option.label}
            </option>
          ))}
        </select>
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Статус</span>
        <select
          value={values.statusType}
          onChange={(e) => onChange('statusType', e.target.value)}
          className={`${fieldSelectClass} ${values.statusType ? '' : selectPlaceholderClass}`}
        >
          <option value="">Все</option>
          {statusOptions.map((option) => (
            <option key={option.id} value={option.id}>
              {option.label}
            </option>
          ))}
        </select>
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Склад</span>
        <select
          value={values.storageType}
          onChange={(e) => onChange('storageType', e.target.value)}
          className={`${fieldSelectClass} ${values.storageType ? '' : selectPlaceholderClass}`}
        >
          <option value="">Все</option>
          {storageOptions.map((option) => (
            <option key={option.id} value={option.id}>
              {option.label}
            </option>
          ))}
        </select>
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Услуга</span>
        <select
          value={values.serviceType}
          onChange={(e) => onChange('serviceType', e.target.value)}
          className={`${fieldSelectClass} ${values.serviceType ? '' : selectPlaceholderClass}`}
        >
          <option value="">Все</option>
          {serviceOptions.map((option) => (
            <option key={option.id} value={option.id}>
              {option.label}
            </option>
          ))}
        </select>
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Менеджер ID</span>
        <input
          type="number"
          min="1"
          value={values.managerId}
          onChange={(e) => onChange('managerId', e.target.value)}
          placeholder="Например, 8"
          className={fieldInputClass}
        />
      </label>

      <label className="block">
        <span className="mb-1 block text-xs font-medium text-slate-500">Дизайнер ID</span>
        <input
          type="number"
          min="1"
          value={values.designerId}
          onChange={(e) => onChange('designerId', e.target.value)}
          placeholder="Например, 12"
          className={fieldInputClass}
        />
      </label>

      <label className="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5">
        <input
          type="checkbox"
          checked={values.archived}
          onChange={(e) => onChange('archived', e.target.checked)}
          className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
        />
        <span className="text-sm text-slate-700">Архивные</span>
      </label>

      <label className="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5">
        <input
          type="checkbox"
          checked={values.deleted}
          onChange={(e) => onChange('deleted', e.target.checked)}
          className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
        />
        <span className="text-sm text-slate-700">Удалённые</span>
      </label>
    </div>
  </div>
);
