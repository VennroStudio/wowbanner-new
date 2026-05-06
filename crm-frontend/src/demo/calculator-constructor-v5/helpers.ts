import type {
  CalculatorAliasOption,
  CalculatorChainBlock,
  CalculatorConst,
  CalculatorField,
  CalculatorRule,
  CalculatorRunResult,
  PreviewValues,
} from './types';

const aliasTypeLabels: Record<CalculatorAliasOption['type'], string> = {
  input: '[инп]',
  checkbox: '[чек]',
  select: '[сел]',
  const: '[кон]',
  computed: '[выч]',
};

const escapeRegExp = (value: string) => value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

export const getAllAliases = (
  fields: CalculatorField[],
  consts: CalculatorConst[],
  chain: CalculatorChainBlock[],
  beforeChainIdx: number,
): CalculatorAliasOption[] => {
  const list: CalculatorAliasOption[] = [];

  fields.forEach((field) => {
    if (field.ftype === 'input' && field.alias) {
      list.push({ alias: field.alias, lbl: field.label, type: 'input' });
    }
    if (field.ftype === 'checkbox' && field.alias) {
      list.push({ alias: field.alias, lbl: field.label, type: 'checkbox' });
    }
    if (field.ftype === 'select' && field.alias) {
      list.push({ alias: field.alias, lbl: field.label, type: 'select' });
    }
  });

  consts.forEach((constant) => {
    if (constant.alias) {
      list.push({ alias: constant.alias, lbl: constant.name, type: 'const' });
    }
  });

  chain.slice(0, beforeChainIdx).forEach((block) => {
    if (block.saveAs) {
      list.push({ alias: block.saveAs, lbl: block.saveAs, type: 'computed' });
    }
  });

  return list;
};

export const getAliasTypeLabel = (type: CalculatorAliasOption['type']) => aliasTypeLabels[type];

export const evaluateFormula = (formula: string, scope: Record<string, number>) => {
  try {
    let expression = formula;

    Object.entries(scope)
      .sort((a, b) => b[0].length - a[0].length)
      .forEach(([key, value]) => {
        expression = expression.replace(
          new RegExp(`(?<![A-Za-zА-Яа-яЁё0-9_])${escapeRegExp(key)}(?![A-Za-zА-Яа-яЁё0-9_])`, 'g'),
          String(value),
        );
      });

    return Function(`"use strict"; return (${expression});`)() as number;
  } catch {
    return null;
  }
};

export const checkRule = (rule: CalculatorRule, scope: Record<string, number>) => {
  const currentValue = scope[rule.var];

  if (currentValue === undefined) {
    return false;
  }

  const targetValue = parseFloat(rule.val);

  switch (rule.op) {
    case '<':
      return currentValue < targetValue;
    case '<=':
      return currentValue <= targetValue;
    case '>':
      return currentValue > targetValue;
    case '>=':
      return currentValue >= targetValue;
    case '==':
      return currentValue == targetValue;
    case '!=':
      return currentValue != targetValue;
    default:
      return false;
  }
};

export const checkConditionBlock = (
  block: Extract<CalculatorChainBlock, { type: 'condition' }>,
  scope: Record<string, number>,
) => {
  if (!block.rules.length) {
    return false;
  }

  const results = block.rules.map((rule) => checkRule(rule, scope));

  return block.logic === 'AND' ? results.every(Boolean) : results.some(Boolean);
};

export const runCalculator = (
  fields: CalculatorField[],
  consts: CalculatorConst[],
  chain: CalculatorChainBlock[],
  userValues: PreviewValues,
): CalculatorRunResult => {
  const scope: Record<string, number> = {};

  fields.forEach((field) => {
    if (field.ftype === 'input' && field.alias) {
      scope[field.alias] = parseFloat(String(userValues[field.id] ?? '0')) || 0;
    }
    if (field.ftype === 'select' && field.alias) {
      const fallbackValue = field.options[0]?.value ?? '0';
      scope[field.alias] = parseFloat(String(userValues[field.id] ?? fallbackValue)) || 0;
    }
    if (field.ftype === 'checkbox' && field.alias) {
      scope[field.alias] = userValues[field.id] ? parseFloat(field.value) || 0 : 0;
    }
  });

  consts.forEach((constant) => {
    if (constant.alias) {
      scope[constant.alias] = constant.value;
    }
  });

  const trace: CalculatorRunResult['trace'] = [];
  let lastResult: number | null = null;
  let lastSaveAs = 'Результат';

  chain.forEach((block, index) => {
    if (block.type === 'condition') {
      const ok = checkConditionBlock(block, scope);
      const condLabel = block.rules
        .map((rule) => `${rule.var} ${rule.op} ${rule.val}`)
        .join(block.logic === 'AND' ? ' И ' : ' ИЛИ ');

      if (ok) {
        const result = evaluateFormula(block.formula, scope);

        if (block.saveAs && result !== null) {
          scope[block.saveAs] = result;
        }

        trace.push({
          step: index + 1,
          type: 'condition',
          condLabel,
          ok,
          formula: block.formula,
          result,
          saveAs: block.saveAs,
        });

        if (result !== null) {
          lastResult = result;
          if (block.saveAs) {
            lastSaveAs = block.saveAs;
          }
        }
      } else {
        trace.push({
          step: index + 1,
          type: 'condition',
          condLabel,
          ok,
          formula: block.formula,
          result: null,
          saveAs: block.saveAs,
        });
      }

      return;
    }

    const result = evaluateFormula(block.formula, scope);

    if (block.saveAs && result !== null) {
      scope[block.saveAs] = result;
    }

    trace.push({
      step: index + 1,
      type: 'formula',
      condLabel: '',
      ok: true,
      formula: block.formula,
      result,
      saveAs: block.saveAs,
    });

    if (result !== null) {
      lastResult = result;
      if (block.saveAs) {
        lastSaveAs = block.saveAs;
      }
    }
  });

  return {
    result: lastResult,
    saveAs: lastSaveAs,
    trace,
  };
};

export const buildCalculatorConfig = (
  name: string,
  description: string,
  fields: CalculatorField[],
  consts: CalculatorConst[],
  chain: CalculatorChainBlock[],
) => ({
  id: 'calc_001',
  name,
  description,
  fields: fields.map((field) => {
    if (field.ftype === 'heading') {
      return { type: 'heading', label: field.label };
    }
    if (field.ftype === 'input') {
      return {
        type: 'input',
        label: field.label,
        alias: field.alias,
        inputType: field.itype,
        placeholder: field.placeholder,
        hint: field.hint,
        required: field.required,
      };
    }
    if (field.ftype === 'checkbox') {
      return {
        type: 'checkbox',
        label: field.label,
        alias: field.alias,
        value: field.value,
        hint: field.hint,
      };
    }
    if (field.ftype === 'select') {
      return {
        type: 'select',
        label: field.label,
        alias: field.alias,
        hint: field.hint,
        options: field.options.map((option) => ({
          label: option.label,
          value: option.value,
        })),
      };
    }

    return {
      type: 'text',
      textType: field.ttype,
      content: field.content,
    };
  }),
  constants: consts.map((constant) => ({
    name: constant.name,
    alias: constant.alias,
    value: constant.value,
  })),
  chain: chain.map((block) => {
    if (block.type === 'condition') {
      return {
        type: block.type,
        formula: block.formula,
        saveAs: block.saveAs || null,
        logic: block.logic,
        rules: block.rules.map((rule) => ({
          variable: rule.var,
          operator: rule.op,
          value: parseFloat(rule.val) || rule.val,
        })),
      };
    }

    return {
      type: block.type,
      formula: block.formula,
      saveAs: block.saveAs || null,
    };
  }),
});

export const buildCalculateRequestSnippet = (fields: CalculatorField[]) => [
  'POST /api/calculators/{id}/calculate',
  '{',
  '  "inputs": {',
  fields
    .filter(
      (field): field is Extract<CalculatorField, { alias: string }> =>
        ['input', 'select', 'checkbox'].includes(field.ftype) && 'alias' in field && Boolean(field.alias),
    )
    .map((field) => `    "${field.alias}": <значение>`)
    .join(',\n'),
  '  }',
  '}',
].join('\n');
