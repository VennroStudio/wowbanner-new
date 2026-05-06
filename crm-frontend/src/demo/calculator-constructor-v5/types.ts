export type DemoTab = 'builder' | 'preview' | 'json';

export type CalculatorFieldType = 'heading' | 'input' | 'checkbox' | 'select' | 'text';
export type CalculatorInputType = 'number' | 'text';
export type CalculatorTextType = 'info' | 'warn' | 'success' | 'tip';
export type CalculatorChainBlockType = 'condition' | 'formula';
export type CalculatorLogic = 'AND' | 'OR';
export type CalculatorRuleOperator = '<' | '<=' | '>' | '>=' | '==' | '!=';

export interface CalculatorMeta {
  name: string;
  desc: string;
}

export interface CalculatorSelectOption {
  id: string;
  label: string;
  value: string;
}

export interface CalculatorHeadingField {
  id: string;
  ftype: 'heading';
  label: string;
}

export interface CalculatorInputField {
  id: string;
  ftype: 'input';
  label: string;
  alias: string;
  itype: CalculatorInputType;
  placeholder: string;
  hint: string;
  required: boolean;
}

export interface CalculatorCheckboxField {
  id: string;
  ftype: 'checkbox';
  label: string;
  alias: string;
  value: string;
  hint: string;
  checked: boolean;
}

export interface CalculatorSelectField {
  id: string;
  ftype: 'select';
  label: string;
  alias: string;
  hint: string;
  required: boolean;
  options: CalculatorSelectOption[];
}

export interface CalculatorTextField {
  id: string;
  ftype: 'text';
  ttype: CalculatorTextType;
  content: string;
}

export type CalculatorField =
  | CalculatorHeadingField
  | CalculatorInputField
  | CalculatorCheckboxField
  | CalculatorSelectField
  | CalculatorTextField;

export interface CalculatorConst {
  id: string;
  name: string;
  alias: string;
  value: number;
}

export interface CalculatorRule {
  id: string;
  var: string;
  op: CalculatorRuleOperator;
  val: string;
}

export interface CalculatorConditionBlock {
  id: string;
  type: 'condition';
  logic: CalculatorLogic;
  rules: CalculatorRule[];
  formula: string;
  saveAs: string;
}

export interface CalculatorFormulaBlock {
  id: string;
  type: 'formula';
  formula: string;
  saveAs: string;
}

export type CalculatorChainBlock = CalculatorConditionBlock | CalculatorFormulaBlock;

export interface CalculatorTraceItem {
  step: number;
  type: CalculatorChainBlockType;
  condLabel: string;
  ok: boolean;
  formula: string;
  result: number | null;
  saveAs: string;
}

export interface CalculatorRunResult {
  result: number | null;
  saveAs: string;
  trace: CalculatorTraceItem[];
}

export interface CalculatorAliasOption {
  alias: string;
  lbl: string;
  type: 'input' | 'checkbox' | 'select' | 'const' | 'computed';
}

export type PreviewValues = Record<string, string | boolean>;
