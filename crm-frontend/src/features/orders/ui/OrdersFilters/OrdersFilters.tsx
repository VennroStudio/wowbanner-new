import { useEffect, useRef, useState } from 'react';
import type { ClientEnumOption } from '@/entities/client';
import type { MaterialSelectOption, MaterialOptionSelectOption } from '@/entities/material';
import type { OrderEnumRef } from '@/entities/order';
import type { PrintingSelectOption } from '@/entities/printing';
import type { UserSelectOption } from '@/entities/user';
import { fieldInputClass, fieldSelectClass } from '@/shared/ui';
import { ORDER_TABLE_COLUMNS, type OrderTableColumnKey } from '../../model/orderTableColumns';

export interface OrdersFilterValues {
  dateFrom: string;
  dateTo: string;
  printIds: number[];
  materialId: string;
  optionId: string;
  docs: string;
  managerId: string;
  designerId: string;
  statusTypes: number[];
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
  managerOptions: UserSelectOption[];
  designerOptions: UserSelectOption[];
  statusOptions: OrderEnumRef[];
  storageOptions: OrderEnumRef[];
  serviceOptions: OrderEnumRef[];
  visibleColumns: Record<OrderTableColumnKey, boolean>;
  onChange: <K extends keyof OrdersFilterValues>(key: K, value: OrdersFilterValues[K]) => void;
  onToggleColumn: (key: OrderTableColumnKey) => void;
  onReset: () => void;
}

const selectPlaceholderClass = 'text-slate-500';
const chipPalette = [
  'bg-emerald-100 text-emerald-700 border-emerald-200',
  'bg-sky-100 text-sky-700 border-sky-200',
  'bg-amber-100 text-amber-700 border-amber-200',
  'bg-rose-100 text-rose-700 border-rose-200',
  'bg-violet-100 text-violet-700 border-violet-200',
  'bg-cyan-100 text-cyan-700 border-cyan-200',
  'bg-lime-100 text-lime-700 border-lime-200',
];

export const OrdersFilters = ({
  values,
  printingOptions,
  materialOptions,
  materialOptionOptions,
  docsOptions,
  managerOptions,
  designerOptions,
  statusOptions,
  storageOptions,
  serviceOptions,
  visibleColumns,
  onChange,
  onToggleColumn,
  onReset,
}: OrdersFiltersProps) => {
  const [showColumnsMenu, setShowColumnsMenu] = useState(false);
  const [isExpanded, setIsExpanded] = useState(true);
  const columnsMenuRef = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    if (!showColumnsMenu) return;

    const handleClickOutside = (event: MouseEvent) => {
      if (columnsMenuRef.current?.contains(event.target as Node)) return;
      setShowColumnsMenu(false);
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, [showColumnsMenu]);

  const togglePrint = (printId: number) => {
    const next = values.printIds.includes(printId)
      ? values.printIds.filter((item) => item !== printId)
      : [...values.printIds, printId];

    onChange('printIds', next);
  };

  const toggleStatus = (statusId: number) => {
    const next = values.statusTypes.includes(statusId)
      ? values.statusTypes.filter((item) => item !== statusId)
      : [...values.statusTypes, statusId];

    onChange('statusTypes', next);
  };

  return (
    <div className="mb-6 rounded-xl border border-slate-200 bg-white p-4 space-y-4">
      <div className="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div className="flex items-center gap-3">
          <h2 className="text-sm font-semibold text-slate-900">Фильтры заказов</h2>
          <button
            type="button"
            onClick={() => setIsExpanded((current) => !current)}
            className="inline-flex items-center rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50 cursor-pointer"
          >
            {isExpanded ? 'Свернуть' : 'Развернуть'}
          </button>
        </div>

        <div className="flex flex-wrap items-center gap-2">
          <label className="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700">
            <input
              type="checkbox"
              checked={values.archived}
              onChange={(e) => onChange('archived', e.target.checked)}
              className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
            />
            Архивные
          </label>

          <label className="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700">
            <input
              type="checkbox"
              checked={values.deleted}
              onChange={(e) => onChange('deleted', e.target.checked)}
              className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
            />
            Удалённые
          </label>

          <div className="relative" ref={columnsMenuRef}>
            <button
              type="button"
              onClick={() => setShowColumnsMenu((current) => !current)}
              className="px-3 py-2 text-sm font-medium text-slate-700 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer"
            >
              Видимость столбцов
            </button>

            {showColumnsMenu ? (
              <div className="absolute right-0 top-full z-20 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-3 shadow-xl">
                <div className="space-y-2 max-h-80 overflow-y-auto">
                  {ORDER_TABLE_COLUMNS.map((column) => (
                    <label key={column.key} className="flex items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-slate-50">
                      <input
                        type="checkbox"
                        checked={visibleColumns[column.key]}
                        onChange={() => onToggleColumn(column.key)}
                        className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                      />
                      <span className="text-sm text-slate-700">{column.label}</span>
                    </label>
                  ))}
                </div>
              </div>
            ) : null}
          </div>

          <button
            type="button"
            onClick={onReset}
            className="px-3 py-2 text-sm font-medium text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer"
          >
            Сбросить фильтры
          </button>
        </div>
      </div>

      {isExpanded ? (
        <>
          <div className="space-y-3">
            <div>
              <p className="mb-2 text-xs font-medium text-slate-500">Статусы</p>
              <div className="flex flex-wrap gap-2">
                {statusOptions.map((option, index) => {
                  const selected = values.statusTypes.includes(option.id);
                  return (
                    <button
                      key={option.id}
                      type="button"
                      onClick={() => toggleStatus(option.id)}
                      className={`rounded-full border px-3 py-1.5 text-xs font-medium transition-colors cursor-pointer ${
                        selected
                          ? chipPalette[index % chipPalette.length]
                          : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50'
                      }`}
                    >
                      {option.label}
                    </button>
                  );
                })}
              </div>
            </div>

            <div>
              <p className="mb-2 text-xs font-medium text-slate-500">Типы печати</p>
              <div className="flex flex-wrap gap-2">
                {printingOptions.map((option, index) => {
                  const selected = values.printIds.includes(option.id);
                  return (
                    <button
                      key={option.id}
                      type="button"
                      onClick={() => togglePrint(option.id)}
                      className={`inline-flex h-9 min-w-9 items-center justify-center rounded-full border px-3 text-xs font-semibold transition-colors cursor-pointer ${
                        selected
                          ? chipPalette[index % chipPalette.length]
                          : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50'
                      }`}
                      title={option.name}
                    >
                      {option.name.slice(0, 2).toUpperCase()}
                    </button>
                  );
                })}
              </div>
            </div>
          </div>

          <div className="grid grid-cols-1 gap-4 xl:grid-cols-12">
            <div className="rounded-xl border border-slate-200 p-3 space-y-3 xl:col-span-4">
              <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Период</p>
              <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
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
              </div>
            </div>

            <div className="rounded-xl border border-slate-200 p-3 space-y-3 xl:col-span-4">
              <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Материалы</p>
              <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
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
              </div>
            </div>

            <div className="rounded-xl border border-slate-200 p-3 space-y-3 xl:col-span-4">
              <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Ответственные</p>
              <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label className="block">
                  <span className="mb-1 block text-xs font-medium text-slate-500">Менеджер</span>
                  <select
                    value={values.managerId}
                    onChange={(e) => onChange('managerId', e.target.value)}
                    className={`${fieldSelectClass} ${values.managerId ? '' : selectPlaceholderClass}`}
                  >
                    <option value="">Все</option>
                    {managerOptions.map((option) => (
                      <option key={option.id} value={option.id}>
                        {option.name}
                      </option>
                    ))}
                  </select>
                </label>

                <label className="block">
                  <span className="mb-1 block text-xs font-medium text-slate-500">Дизайнер</span>
                  <select
                    value={values.designerId}
                    onChange={(e) => onChange('designerId', e.target.value)}
                    className={`${fieldSelectClass} ${values.designerId ? '' : selectPlaceholderClass}`}
                  >
                    <option value="">Все</option>
                    {designerOptions.map((option) => (
                      <option key={option.id} value={option.id}>
                        {option.name}
                      </option>
                    ))}
                  </select>
                </label>
              </div>
            </div>

            <label className="block xl:col-span-4">
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

            <label className="block xl:col-span-4">
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

            <label className="block xl:col-span-4">
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
          </div>
        </>
      ) : null}
    </div>
  );
};
