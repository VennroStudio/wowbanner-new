import type {
  CalculatorChainBlock,
  CalculatorConst,
  CalculatorField,
  CalculatorMeta,
} from './types';

const createIdFactory = () => {
  let uid = 300;

  return () => `u${uid++}`;
};

export const createInitialCalculatorDemoState = () => {
  const nextId = createIdFactory();

  const meta: CalculatorMeta = {
    name: 'Плоттерная резка',
    desc: 'Расчёт стоимости изделия',
  };

  const fields: CalculatorField[] = [
    { id: nextId(), ftype: 'heading', label: 'Материал' },
    {
      id: nextId(),
      ftype: 'select',
      label: 'Материал',
      alias: 'MAT',
      hint: '',
      required: true,
      options: [
        { id: nextId(), label: 'Оракал с печатью', value: '1' },
        { id: nextId(), label: 'Простая плёнка', value: '0.8' },
      ],
    },
    { id: nextId(), ftype: 'heading', label: 'Размер' },
    {
      id: nextId(),
      ftype: 'input',
      label: 'Ширина (мм)',
      alias: 'W',
      itype: 'number',
      placeholder: 'Ширина',
      hint: '',
      required: true,
    },
    {
      id: nextId(),
      ftype: 'input',
      label: 'Высота (мм)',
      alias: 'H',
      itype: 'number',
      placeholder: 'Высота',
      hint: '',
      required: true,
    },
    {
      id: nextId(),
      ftype: 'input',
      label: 'Количество (шт)',
      alias: 'V',
      itype: 'number',
      placeholder: 'Количество',
      hint: '',
      required: true,
    },
    { id: nextId(), ftype: 'heading', label: 'Сложность' },
    {
      id: nextId(),
      ftype: 'checkbox',
      label: 'Сложная форма',
      alias: 'COMPLEX',
      value: '0.2',
      hint: 'Доп. коэффициент',
      checked: false,
    },
    {
      id: nextId(),
      ftype: 'text',
      ttype: 'warn',
      content: 'Не берём в резку макеты с толщиной линий менее 2мм',
    },
  ];

  const consts: CalculatorConst[] = [
    { id: nextId(), name: 'Базовая цена', alias: 'BASE', value: 5 },
  ];

  const chain: CalculatorChainBlock[] = [
    {
      id: nextId(),
      type: 'condition',
      logic: 'AND',
      rules: [{ id: nextId(), var: 'V', op: '<=', val: '9' }],
      formula: '(W*H/1000000)*BASE*MAT*V',
      saveAs: 'PRICE',
    },
    {
      id: nextId(),
      type: 'condition',
      logic: 'AND',
      rules: [{ id: nextId(), var: 'V', op: '>=', val: '10' }],
      formula: '(W*H/1000000)*BASE*MAT*V*0.9',
      saveAs: 'PRICE',
    },
    {
      id: nextId(),
      type: 'formula',
      formula: 'PRICE+(PRICE*COMPLEX)',
      saveAs: 'ИТОГО',
    },
  ];

  return { meta, fields, consts, chain, nextId };
};
