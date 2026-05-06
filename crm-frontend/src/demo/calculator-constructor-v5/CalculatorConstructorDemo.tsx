import { useState } from 'react';
import './calculatorConstructorDemo.css';
import { buildCalculateRequestSnippet, buildCalculatorConfig, getAllAliases, getAliasTypeLabel, runCalculator } from './helpers';
import { createInitialCalculatorDemoState } from './initialState';
import type {
  CalculatorChainBlock,
  CalculatorConst,
  CalculatorField,
  CalculatorRule,
  CalculatorTextType,
  DemoTab,
  PreviewValues,
} from './types';

const initialState = createInitialCalculatorDemoState();

const ruleOperators: CalculatorRule['op'][] = ['<', '<=', '>', '>=', '==', '!='];

const textTypeOptions: { value: CalculatorTextType; label: string }[] = [
  { value: 'info', label: 'Инфо' },
  { value: 'warn', label: 'Предупреждение' },
  { value: 'success', label: 'Совет' },
  { value: 'tip', label: 'Подсказка' },
];

const nextId = initialState.nextId;

const createField = (ftype: CalculatorField['ftype']): CalculatorField => {
  if (ftype === 'heading') {
    return { id: nextId(), ftype, label: 'Новый раздел' };
  }

  if (ftype === 'input') {
    return {
      id: nextId(),
      ftype,
      label: '',
      alias: '',
      itype: 'number',
      placeholder: '',
      hint: '',
      required: true,
    };
  }

  if (ftype === 'checkbox') {
    return {
      id: nextId(),
      ftype,
      label: '',
      alias: '',
      value: '1',
      hint: '',
      checked: false,
    };
  }

  if (ftype === 'select') {
    return {
      id: nextId(),
      ftype,
      label: '',
      alias: '',
      hint: '',
      required: true,
      options: [{ id: nextId(), label: 'Вариант 1', value: '1' }],
    };
  }

  return {
    id: nextId(),
    ftype,
    content: '',
    ttype: 'info',
  };
};

const createConst = (): CalculatorConst => ({
  id: nextId(),
  name: '',
  alias: '',
  value: 0,
});

const createChainBlock = (type: CalculatorChainBlock['type']): CalculatorChainBlock => {
  if (type === 'condition') {
    return {
      id: nextId(),
      type,
      logic: 'AND',
      rules: [{ id: nextId(), var: '', op: '<', val: '' }],
      formula: '',
      saveAs: '',
    };
  }

  return {
    id: nextId(),
    type,
    formula: '',
    saveAs: '',
  };
};

const moveItem = <T,>(items: T[], index: number, direction: -1 | 1) => {
  const targetIndex = index + direction;

  if (targetIndex < 0 || targetIndex >= items.length) {
    return items;
  }

  const next = [...items];
  [next[index], next[targetIndex]] = [next[targetIndex], next[index]];
  return next;
};

export const CalculatorConstructorDemo = () => {
  const [activeTab, setActiveTab] = useState<DemoTab>('builder');
  const [meta, setMeta] = useState(initialState.meta);
  const [fields, setFields] = useState<CalculatorField[]>(initialState.fields);
  const [consts, setConsts] = useState<CalculatorConst[]>(initialState.consts);
  const [chain, setChain] = useState<CalculatorChainBlock[]>(initialState.chain);
  const [previewValues, setPreviewValues] = useState<PreviewValues>({});
  const [copyNotice, setCopyNotice] = useState('');

  const calculation = runCalculator(fields, consts, chain, previewValues);
  const calculatorConfig = buildCalculatorConfig(meta.name, meta.desc, fields, consts, chain);
  const calculatorJson = JSON.stringify(calculatorConfig, null, 2);
  const calculateSnippet = buildCalculateRequestSnippet(fields);

  const updateField = (fieldId: string, updater: (field: CalculatorField) => CalculatorField) => {
    setFields((current) => current.map((field) => (field.id === fieldId ? updater(field) : field)));
  };

  const updateConst = (constId: string, updater: (item: CalculatorConst) => CalculatorConst) => {
    setConsts((current) => current.map((item) => (item.id === constId ? updater(item) : item)));
  };

  const updateChainBlock = (
    blockId: string,
    updater: (block: CalculatorChainBlock) => CalculatorChainBlock,
  ) => {
    setChain((current) => current.map((block) => (block.id === blockId ? updater(block) : block)));
  };

  const copyJson = async () => {
    try {
      await navigator.clipboard.writeText(calculatorJson);
      setCopyNotice('JSON скопирован');
      window.setTimeout(() => setCopyNotice(''), 1800);
    } catch {
      setCopyNotice('Не удалось скопировать JSON');
      window.setTimeout(() => setCopyNotice(''), 1800);
    }
  };

  return (
    <div className="calc-demo calc-demo__page">
      <div className="calc-demo__page-header">
        <div>
          <h1 className="calc-demo__page-title">Калькуляторы</h1>
        </div>
        <div className="calc-demo__badge">Demo sandbox</div>
      </div>

      <div className="calc-demo__tabs">
        {[
          ['builder', 'Конструктор'],
          ['preview', 'Превью'],
          ['json', 'JSON / API'],
        ].map(([value, label]) => (
          <button
            key={value}
            type="button"
            className={`calc-demo__tab ${activeTab === value ? 'calc-demo__tab--active' : ''}`}
            onClick={() => setActiveTab(value as DemoTab)}
          >
            {label}
          </button>
        ))}
      </div>

      {activeTab === 'builder' ? (
        <>
          <div className="calc-demo__card">
            <span className="calc-demo__label">Настройки</span>
            <div className="calc-demo__field-grid calc-demo__field-grid--2">
              <div>
                <label className="calc-demo__field-label">Название калькулятора</label>
                <input
                  className="calc-demo__input"
                  value={meta.name}
                  onChange={(event) => setMeta((current) => ({ ...current, name: event.target.value }))}
                />
              </div>
              <div>
                <label className="calc-demo__field-label">Описание</label>
                <input
                  className="calc-demo__input"
                  value={meta.desc}
                  onChange={(event) => setMeta((current) => ({ ...current, desc: event.target.value }))}
                />
              </div>
            </div>
          </div>

          <div className="calc-demo__card">
            <span className="calc-demo__label">Поля калькулятора</span>
            {fields.map((field, index) => {
              const commonActions = (
                <div className="calc-demo__block-actions">
                  <button
                    type="button"
                    className="calc-demo__button calc-demo__button--ghost calc-demo__button--sm"
                    disabled={index === 0}
                    onClick={() => setFields((current) => moveItem(current, index, -1))}
                  >
                    ↑
                  </button>
                  <button
                    type="button"
                    className="calc-demo__button calc-demo__button--ghost calc-demo__button--sm"
                    disabled={index === fields.length - 1}
                    onClick={() => setFields((current) => moveItem(current, index, 1))}
                  >
                    ↓
                  </button>
                  <button
                    type="button"
                    className="calc-demo__button calc-demo__button--danger calc-demo__button--sm"
                    onClick={() => setFields((current) => current.filter((item) => item.id !== field.id))}
                  >
                    ✕
                  </button>
                </div>
              );

              if (field.ftype === 'heading') {
                return (
                  <div key={field.id} className="calc-demo__block calc-demo__block--info">
                    <div className="calc-demo__block-header">
                      <span className="calc-demo__tag calc-demo__tag--info">Заголовок</span>
                      {commonActions}
                    </div>
                    <input
                      className="calc-demo__input"
                      value={field.label}
                      placeholder="Название раздела"
                      onChange={(event) =>
                        updateField(field.id, (current) => ({ ...current, label: event.target.value }))
                      }
                    />
                  </div>
                );
              }

              if (field.ftype === 'input') {
                return (
                  <div key={field.id} className="calc-demo__block">
                    <div className="calc-demo__block-header">
                      <span className="calc-demo__tag calc-demo__tag--teal">Инпут</span>
                      {commonActions}
                    </div>
                    <div className="calc-demo__field-grid calc-demo__field-grid--3">
                      <div>
                        <label className="calc-demo__field-label">Название</label>
                        <input
                          className="calc-demo__input"
                          value={field.label}
                          placeholder="Название"
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, label: event.target.value }))
                          }
                        />
                      </div>
                      <div>
                        <label className="calc-demo__field-label">Alias</label>
                        <input
                          className="calc-demo__input calc-demo__mono"
                          value={field.alias}
                          maxLength={12}
                          placeholder="W"
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, alias: event.target.value }))
                          }
                        />
                      </div>
                      <div>
                        <label className="calc-demo__field-label">Тип</label>
                        <select
                          className="calc-demo__select"
                          value={field.itype}
                          onChange={(event) =>
                            updateField(field.id, (current) => ({
                              ...current,
                              itype: event.target.value as 'number' | 'text',
                            }))
                          }
                        >
                          <option value="number">Число</option>
                          <option value="text">Текст</option>
                        </select>
                      </div>
                    </div>
                    <div className="calc-demo__field-grid calc-demo__field-grid--2">
                      <div>
                        <label className="calc-demo__field-label">Placeholder</label>
                        <input
                          className="calc-demo__input"
                          value={field.placeholder}
                          placeholder="Введите..."
                          onChange={(event) =>
                            updateField(field.id, (current) => ({
                              ...current,
                              placeholder: event.target.value,
                            }))
                          }
                        />
                      </div>
                      <div>
                        <label className="calc-demo__field-label">Подсказка / ед.изм.</label>
                        <input
                          className="calc-demo__input"
                          value={field.hint}
                          placeholder="мм, шт..."
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, hint: event.target.value }))
                          }
                        />
                      </div>
                    </div>
                  </div>
                );
              }

              if (field.ftype === 'checkbox') {
                return (
                  <div key={field.id} className="calc-demo__block">
                    <div className="calc-demo__block-header">
                      <span className="calc-demo__tag calc-demo__tag--purple">Чекбокс</span>
                      {commonActions}
                    </div>
                    <div className="calc-demo__field-grid calc-demo__field-grid--4">
                      <div>
                        <label className="calc-demo__field-label">Название</label>
                        <input
                          className="calc-demo__input"
                          value={field.label}
                          placeholder="Название"
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, label: event.target.value }))
                          }
                        />
                      </div>
                      <div>
                        <label className="calc-demo__field-label">Alias</label>
                        <input
                          className="calc-demo__input calc-demo__mono"
                          value={field.alias}
                          maxLength={12}
                          placeholder="CBX"
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, alias: event.target.value }))
                          }
                        />
                      </div>
                      <div>
                        <label className="calc-demo__field-label">Подсказка</label>
                        <input
                          className="calc-demo__input"
                          value={field.hint}
                          placeholder="Доп. коэф."
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, hint: event.target.value }))
                          }
                        />
                      </div>
                      <div>
                        <label className="calc-demo__field-label">Значение ✓</label>
                        <input
                          className="calc-demo__input"
                          value={field.value}
                          placeholder="1"
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, value: event.target.value }))
                          }
                        />
                      </div>
                    </div>
                  </div>
                );
              }

              if (field.ftype === 'select') {
                return (
                  <div key={field.id} className="calc-demo__block">
                    <div className="calc-demo__block-header">
                      <span className="calc-demo__tag calc-demo__tag--coral">Селект</span>
                      {commonActions}
                    </div>
                    <div className="calc-demo__field-grid calc-demo__field-grid--3">
                      <div>
                        <label className="calc-demo__field-label">Название</label>
                        <input
                          className="calc-demo__input"
                          value={field.label}
                          placeholder="Материал"
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, label: event.target.value }))
                          }
                        />
                      </div>
                      <div>
                        <label className="calc-demo__field-label">Alias</label>
                        <input
                          className="calc-demo__input calc-demo__mono"
                          value={field.alias}
                          maxLength={12}
                          placeholder="MAT"
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, alias: event.target.value }))
                          }
                        />
                      </div>
                      <div>
                        <label className="calc-demo__field-label">Подсказка</label>
                        <input
                          className="calc-demo__input"
                          value={field.hint}
                          placeholder="..."
                          onChange={(event) =>
                            updateField(field.id, (current) => ({ ...current, hint: event.target.value }))
                          }
                        />
                      </div>
                    </div>

                    <div className="calc-demo__field-label">Варианты (название → числовое значение alias)</div>
                    {field.options.map((option) => (
                      <div key={option.id} className="calc-demo__inline-row">
                        <input
                          className="calc-demo__input"
                          style={{ flex: 2 }}
                          value={option.label}
                          placeholder="Название"
                          onChange={(event) =>
                            updateField(field.id, (current) => ({
                              ...(current as Extract<CalculatorField, { ftype: 'select' }>),
                              options: (current as Extract<CalculatorField, { ftype: 'select' }>).options.map((item) =>
                                item.id === option.id ? { ...item, label: event.target.value } : item,
                              ),
                            }))
                          }
                        />
                        <input
                          className="calc-demo__input calc-demo__mono"
                          style={{ width: 90 }}
                          value={option.value}
                          placeholder="1.0"
                          onChange={(event) =>
                            updateField(field.id, (current) => ({
                              ...(current as Extract<CalculatorField, { ftype: 'select' }>),
                              options: (current as Extract<CalculatorField, { ftype: 'select' }>).options.map((item) =>
                                item.id === option.id ? { ...item, value: event.target.value } : item,
                              ),
                            }))
                          }
                        />
                        {field.options.length > 1 ? (
                          <button
                            type="button"
                            className="calc-demo__button calc-demo__button--danger calc-demo__button--sm"
                            onClick={() =>
                              updateField(field.id, (current) => ({
                                ...(current as Extract<CalculatorField, { ftype: 'select' }>),
                                options: (current as Extract<CalculatorField, { ftype: 'select' }>).options.filter(
                                  (item) => item.id !== option.id,
                                ),
                              }))
                            }
                          >
                            ✕
                          </button>
                        ) : null}
                      </div>
                    ))}
                    <button
                      type="button"
                      className="calc-demo__button calc-demo__button--sm"
                      onClick={() =>
                        updateField(field.id, (current) => ({
                          ...(current as Extract<CalculatorField, { ftype: 'select' }>),
                          options: [
                            ...(current as Extract<CalculatorField, { ftype: 'select' }>).options,
                            { id: nextId(), label: '', value: '1' },
                          ],
                        }))
                      }
                    >
                      + вариант
                    </button>
                  </div>
                );
              }

              return (
                <div key={field.id} className="calc-demo__block">
                  <div className="calc-demo__block-header">
                    <span className="calc-demo__tag calc-demo__tag--gray">Текст / заметка</span>
                    {commonActions}
                  </div>
                  <div className="calc-demo__field-grid calc-demo__field-grid--2">
                    <div>
                      <label className="calc-demo__field-label">Тип</label>
                      <select
                        className="calc-demo__select"
                        value={field.ttype}
                        onChange={(event) =>
                          updateField(field.id, (current) => ({
                            ...current,
                            ttype: event.target.value as CalculatorTextType,
                          }))
                        }
                      >
                        {textTypeOptions.map((option) => (
                          <option key={option.value} value={option.value}>
                            {option.label}
                          </option>
                        ))}
                      </select>
                    </div>
                    <div>
                      <label className="calc-demo__field-label">Текст</label>
                      <input
                        className="calc-demo__input"
                        value={field.content}
                        placeholder="Текст заметки..."
                        onChange={(event) =>
                          updateField(field.id, (current) => ({ ...current, content: event.target.value }))
                        }
                      />
                    </div>
                  </div>
                </div>
              );
            })}

            <div className="calc-demo__add-menu">
              <span className="calc-demo__helper">Добавить:</span>
              <button type="button" className="calc-demo__button calc-demo__button--sm" onClick={() => setFields((current) => [...current, createField('heading')])}>
                + Заголовок
              </button>
              <button type="button" className="calc-demo__button calc-demo__button--sm" onClick={() => setFields((current) => [...current, createField('input')])}>
                + Инпут
              </button>
              <button type="button" className="calc-demo__button calc-demo__button--sm" onClick={() => setFields((current) => [...current, createField('checkbox')])}>
                + Чекбокс
              </button>
              <button type="button" className="calc-demo__button calc-demo__button--sm" onClick={() => setFields((current) => [...current, createField('select')])}>
                + Селект
              </button>
              <button type="button" className="calc-demo__button calc-demo__button--sm" onClick={() => setFields((current) => [...current, createField('text')])}>
                + Заметка
              </button>
            </div>
          </div>

          <div className="calc-demo__card">
            <span className="calc-demo__label">Константы</span>
            {consts.map((constant) => (
              <div key={constant.id} className="calc-demo__inline-row">
                <input
                  className="calc-demo__input"
                  style={{ flex: 2 }}
                  value={constant.name}
                  placeholder="Название"
                  onChange={(event) =>
                    updateConst(constant.id, (current) => ({ ...current, name: event.target.value }))
                  }
                />
                <input
                  className="calc-demo__input"
                  style={{ width: 90 }}
                  type="number"
                  value={Number.isFinite(constant.value) ? constant.value : 0}
                  onChange={(event) =>
                    updateConst(constant.id, (current) => ({
                      ...current,
                      value: parseFloat(event.target.value) || 0,
                    }))
                  }
                />
                <input
                  className="calc-demo__input calc-demo__mono"
                  style={{ width: 90 }}
                  maxLength={10}
                  value={constant.alias}
                  placeholder="A"
                  onChange={(event) =>
                    updateConst(constant.id, (current) => ({ ...current, alias: event.target.value }))
                  }
                />
                <button
                  type="button"
                  className="calc-demo__button calc-demo__button--danger calc-demo__button--sm"
                  onClick={() => setConsts((current) => current.filter((item) => item.id !== constant.id))}
                >
                  ✕
                </button>
              </div>
            ))}
            <button type="button" className="calc-demo__button calc-demo__button--sm" onClick={() => setConsts((current) => [...current, createConst()])}>
              + Константа
            </button>
          </div>

          <div className="calc-demo__card">
            <span className="calc-demo__label">Цепочка расчётов</span>
            <p className="calc-demo__helper" style={{ marginBottom: '0.75rem' }}>
              Все шаги выполняются по очереди. Условные — только если условие совпадает. Формулы
              без условия — всегда.
            </p>
            {chain.map((block, index) => (
              <div key={block.id}>
                <div className={`calc-demo__block ${block.type === 'formula' ? 'calc-demo__block--muted' : ''}`}>
                  <div className="calc-demo__block-header">
                    <span className={`calc-demo__tag ${block.type === 'condition' ? 'calc-demo__tag--info' : 'calc-demo__tag--gray'}`}>
                      {block.type === 'condition' ? `Условие ${index + 1}` : `Формула ${index + 1} (всегда)`}
                    </span>
                    <div className="calc-demo__block-actions">
                      <button
                        type="button"
                        className="calc-demo__button calc-demo__button--ghost calc-demo__button--sm"
                        disabled={index === 0}
                        onClick={() => setChain((current) => moveItem(current, index, -1))}
                      >
                        ↑
                      </button>
                      <button
                        type="button"
                        className="calc-demo__button calc-demo__button--ghost calc-demo__button--sm"
                        disabled={index === chain.length - 1}
                        onClick={() => setChain((current) => moveItem(current, index, 1))}
                      >
                        ↓
                      </button>
                      {block.type === 'condition' ? (
                        <button
                          type="button"
                          className="calc-demo__button calc-demo__button--sm"
                          onClick={() =>
                            updateChainBlock(block.id, (current) => ({
                              ...(current as Extract<CalculatorChainBlock, { type: 'condition' }>),
                              rules: [
                                ...(current as Extract<CalculatorChainBlock, { type: 'condition' }>).rules,
                                { id: nextId(), var: '', op: '<', val: '' },
                              ],
                            }))
                          }
                        >
                          + правило
                        </button>
                      ) : null}
                      <button
                        type="button"
                        className="calc-demo__button calc-demo__button--danger calc-demo__button--sm"
                        onClick={() => setChain((current) => current.filter((item) => item.id !== block.id))}
                      >
                        ✕
                      </button>
                    </div>
                  </div>

                  {block.type === 'condition' ? (
                    <>
                      {block.rules.map((rule, ruleIndex) => (
                        <div key={rule.id} className="calc-demo__rule-row">
                          {ruleIndex === 0 ? (
                            <span className="calc-demo__helper" style={{ width: 34 }}>
                              Если
                            </span>
                          ) : (
                            <select
                              className="calc-demo__select"
                              style={{ width: 68 }}
                              value={block.logic}
                              onChange={(event) =>
                                updateChainBlock(block.id, (current) => ({
                                  ...current,
                                  logic: event.target.value as 'AND' | 'OR',
                                }))
                              }
                            >
                              <option value="AND">И</option>
                              <option value="OR">ИЛИ</option>
                            </select>
                          )}

                          <select
                            className="calc-demo__select"
                            style={{ minWidth: 110, width: 'auto' }}
                            value={rule.var}
                              onChange={(event) =>
                                updateChainBlock(block.id, (current) => ({
                                  ...(current as Extract<CalculatorChainBlock, { type: 'condition' }>),
                                  rules: (current as Extract<CalculatorChainBlock, { type: 'condition' }>).rules.map((item) =>
                                    item.id === rule.id ? { ...item, var: event.target.value } : item,
                                  ),
                                }))
                              }
                          >
                            <option value="">—</option>
                            {getAllAliases(fields, consts, chain, index).map((alias) => (
                              <option key={`${alias.type}-${alias.alias}`} value={alias.alias}>
                                {getAliasTypeLabel(alias.type)} {alias.alias}
                              </option>
                            ))}
                          </select>

                          <select
                            className="calc-demo__select"
                            style={{ width: 66 }}
                            value={rule.op}
                              onChange={(event) =>
                                updateChainBlock(block.id, (current) => ({
                                  ...(current as Extract<CalculatorChainBlock, { type: 'condition' }>),
                                  rules: (current as Extract<CalculatorChainBlock, { type: 'condition' }>).rules.map((item) =>
                                    item.id === rule.id
                                      ? { ...item, op: event.target.value as CalculatorRule['op'] }
                                      : item,
                                ),
                              }))
                            }
                          >
                            {ruleOperators.map((operator) => (
                              <option key={operator} value={operator}>
                                {operator}
                              </option>
                            ))}
                          </select>

                          <input
                            className="calc-demo__input"
                            style={{ width: 80 }}
                            value={rule.val}
                            placeholder="10"
                              onChange={(event) =>
                                updateChainBlock(block.id, (current) => ({
                                  ...(current as Extract<CalculatorChainBlock, { type: 'condition' }>),
                                  rules: (current as Extract<CalculatorChainBlock, { type: 'condition' }>).rules.map((item) =>
                                    item.id === rule.id ? { ...item, val: event.target.value } : item,
                                  ),
                                }))
                            }
                          />

                          {block.rules.length > 1 ? (
                            <button
                              type="button"
                              className="calc-demo__button calc-demo__button--danger calc-demo__button--sm"
                              onClick={() =>
                                updateChainBlock(block.id, (current) => ({
                                  ...(current as Extract<CalculatorChainBlock, { type: 'condition' }>),
                                  rules: (current as Extract<CalculatorChainBlock, { type: 'condition' }>).rules.filter(
                                    (item) => item.id !== rule.id,
                                  ),
                                }))
                              }
                            >
                              ✕
                            </button>
                          ) : null}
                        </div>
                      ))}
                      <div className="calc-demo__divider" />
                    </>
                  ) : null}

                  <div className="calc-demo__save-row">
                    <span className="calc-demo__helper">→</span>
                    <input
                      className="calc-demo__input calc-demo__mono"
                      value={block.formula}
                      placeholder="(W*H/1000000)*BASE*MAT*V"
                      onChange={(event) =>
                        updateChainBlock(block.id, (current) => ({ ...current, formula: event.target.value }))
                      }
                    />
                  </div>
                  <div className="calc-demo__save-row">
                    <span className="calc-demo__helper">Сохранить как:</span>
                    <input
                      className="calc-demo__input calc-demo__mono"
                      value={block.saveAs}
                      placeholder="необязательно"
                      onChange={(event) =>
                        updateChainBlock(block.id, (current) => ({ ...current, saveAs: event.target.value }))
                      }
                    />
                  </div>
                </div>

                {index !== chain.length - 1 ? <div className="calc-demo__chain-arrow">↓ следующий шаг</div> : null}
              </div>
            ))}

            <div className="calc-demo__add-menu">
              <button type="button" className="calc-demo__button calc-demo__button--sm" onClick={() => setChain((current) => [...current, createChainBlock('condition')])}>
                + Условие + формула
              </button>
              <button type="button" className="calc-demo__button calc-demo__button--sm" onClick={() => setChain((current) => [...current, createChainBlock('formula')])}>
                + Формула (всегда)
              </button>
            </div>
          </div>

          <div className="calc-demo__footer-actions" style={{ justifyContent: 'flex-end' }}>
            <button type="button" className="calc-demo__button calc-demo__button--success" onClick={() => setActiveTab('preview')}>
              Превью →
            </button>
            <button type="button" className="calc-demo__button calc-demo__button--primary" onClick={() => setActiveTab('json')}>
              JSON →
            </button>
          </div>
        </>
      ) : null}

      {activeTab === 'preview' ? (
        <>
          <div className="calc-demo__preview">
            <div className="calc-demo__card">
              <div className="calc-demo__preview-heading">{meta.name}</div>
              {meta.desc ? <div className="calc-demo__preview-desc">{meta.desc}</div> : null}
              <div className="calc-demo__divider" />
              <div style={{ marginTop: '0.75rem' }}>
                {fields.map((field) => {
                  if (field.ftype === 'heading') {
                    return (
                      <div key={field.id} className="calc-demo__preview-divider-title">
                        {field.label}
                      </div>
                    );
                  }

                  if (field.ftype === 'input') {
                    return (
                      <div key={field.id} className="calc-demo__preview-field">
                        <label className="calc-demo__preview-field-label">
                          {field.label}
                          {field.required ? <span className="calc-demo__required"> *</span> : null}
                        </label>
                        {field.hint ? <div className="calc-demo__hint">{field.hint}</div> : null}
                        <input
                          className="calc-demo__input"
                          type={field.itype}
                          placeholder={field.placeholder}
                          value={String(previewValues[field.id] ?? '')}
                          onChange={(event) =>
                            setPreviewValues((current) => ({
                              ...current,
                              [field.id]: event.target.value,
                            }))
                          }
                        />
                      </div>
                    );
                  }

                  if (field.ftype === 'checkbox') {
                    return (
                      <div key={field.id} className="calc-demo__preview-field">
                        <label className="calc-demo__checkbox">
                          <input
                            type="checkbox"
                            checked={Boolean(previewValues[field.id])}
                            onChange={(event) =>
                              setPreviewValues((current) => ({
                                ...current,
                                [field.id]: event.target.checked,
                              }))
                            }
                          />
                          <span>{field.label}</span>
                          {field.hint ? <span className="calc-demo__checkbox-note">{field.hint}</span> : null}
                        </label>
                      </div>
                    );
                  }

                  if (field.ftype === 'select') {
                    const currentValue = typeof previewValues[field.id] === 'string'
                      ? String(previewValues[field.id])
                      : field.options[0]?.value ?? '';

                    return (
                      <div key={field.id} className="calc-demo__preview-field">
                        <label className="calc-demo__preview-field-label">
                          {field.label}
                          {field.required ? <span className="calc-demo__required"> *</span> : null}
                        </label>
                        {field.hint ? <div className="calc-demo__hint">{field.hint}</div> : null}
                        <select
                          className="calc-demo__select"
                          value={currentValue}
                          onChange={(event) =>
                            setPreviewValues((current) => ({
                              ...current,
                              [field.id]: event.target.value,
                            }))
                          }
                        >
                          {field.options.map((option) => (
                            <option key={option.id} value={option.value}>
                              {option.label}
                            </option>
                          ))}
                        </select>
                      </div>
                    );
                  }

                  const noteClassName = {
                    info: 'calc-demo__note calc-demo__note--info',
                    warn: 'calc-demo__note calc-demo__note--warn',
                    success: 'calc-demo__note calc-demo__note--success',
                    tip: 'calc-demo__note calc-demo__note--tip',
                  }[field.ttype];

                  return (
                    <div key={field.id} className={noteClassName}>
                      {field.content}
                    </div>
                  );
                })}
                <button type="button" className="calc-demo__button calc-demo__button--primary" style={{ width: '100%', padding: '0.75rem' }}>
                  Рассчитать
                </button>
              </div>
            </div>

            <div className="calc-demo__card">
              <div className="calc-demo__result">
                <div className="calc-demo__result-label">Итоговая стоимость</div>
                <div className="calc-demo__result-value">
                  {calculation.result !== null ? `${calculation.result.toFixed(2)} ₽` : '—'}
                </div>
              </div>

              {calculation.trace.length ? (
                <>
                  <div className="calc-demo__divider" />
                  <div className="calc-demo__trace-title">Трассировка</div>
                  {calculation.trace.map((item) => (
                    <div key={`${item.step}-${item.formula}`} className="calc-demo__trace-item">
                      <span className={`calc-demo__tag ${item.ok ? 'calc-demo__tag--success' : 'calc-demo__tag--gray'}`}>
                        Шаг {item.step}
                      </span>
                      {item.type === 'condition' ? <span className="calc-demo__helper">{item.condLabel}</span> : null}
                      {item.ok && item.result !== null ? (
                        <>
                          <span className="calc-demo__mono calc-demo__helper">
                            {item.formula} = {item.result.toFixed(2)}
                          </span>
                          {item.saveAs ? <span className="calc-demo__scope-pill">→ {item.saveAs}</span> : null}
                        </>
                      ) : null}
                      {!item.ok ? <span className="calc-demo__helper">пропущен</span> : null}
                    </div>
                  ))}
                </>
              ) : null}
            </div>
          </div>

          <div className="calc-demo__footer-actions" style={{ justifyContent: 'flex-end' }}>
            <button type="button" className="calc-demo__button calc-demo__button--ghost" onClick={() => setActiveTab('builder')}>
              ← Конструктор
            </button>
          </div>
        </>
      ) : null}

      {activeTab === 'json' ? (
        <>
          <div className="calc-demo__card">
            <span className="calc-demo__label">Сохранение (POST /api/calculators)</span>
            <div className="calc-demo__json">{calculatorJson}</div>
          </div>
          <div className="calc-demo__card">
            <span className="calc-demo__label">Запрос расчёта</span>
            <div className="calc-demo__json">{calculateSnippet}</div>
          </div>
          <div className="calc-demo__footer-actions" style={{ justifyContent: 'space-between' }}>
            <button type="button" className="calc-demo__button calc-demo__button--ghost" onClick={() => setActiveTab('builder')}>
              ← Конструктор
            </button>
            <div className="calc-demo__row-actions">
              {copyNotice ? <span className="calc-demo__helper">{copyNotice}</span> : null}
              <button type="button" className="calc-demo__button calc-demo__button--primary" onClick={copyJson}>
                Скопировать JSON
              </button>
            </div>
          </div>
        </>
      ) : null}
    </div>
  );
};
