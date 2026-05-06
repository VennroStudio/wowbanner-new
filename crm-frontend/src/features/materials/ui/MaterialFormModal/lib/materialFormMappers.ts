import type { Material } from '@/entities/material';
import type { CreateMaterialBody, UpdateMaterialBody } from '@/entities/material';
import type {
  MaterialAreaPricingFormValue,
  MaterialCutPricingFormValue,
  MaterialFormValues,
  MaterialOptionFormValues,
  MaterialPiecePricingFormValue,
} from './materialFormSchema';
import { buildAreaKey, buildCutKey, buildPieceKey } from './materialFormSchema';

const isFilled = (value?: string) => (value ?? '').trim().length > 0;

const hasAreaRowValues = (row: MaterialAreaPricingFormValue) =>
  isFilled(row.price) || isFilled(row.cost) || isFilled(row.printHours);

const hasPieceRowValues = (row: MaterialPiecePricingFormValue) =>
  isFilled(row.price) || isFilled(row.cost) || isFilled(row.printHours);

const hasCutRowValues = (row: MaterialCutPricingFormValue) => isFilled(row.price);

const sortById = <T extends { id: number }>(items: T[]) => [...items].sort((a, b) => a.id - b.id);

export function mapMaterialToFormValues(material: Material): MaterialFormValues {
  return {
    name: material.name,
    description: material.description ?? '',
    options: sortById(material.options ?? []).map((option) => ({
      id: option.id,
      name: option.name,
      pricingTypeId: option.pricingType.id,
      isCut: option.isCut,
      processings: sortById(option.processings ?? []).map((processing) => ({
        id: processing.id,
        processingId: processing.processingId,
      })),
      pricingByArea: Object.fromEntries(
        sortById(option.pricingByArea ?? []).map((row) => [
          buildAreaKey(row.areaRangeType.id, row.dpiType.id),
          {
            id: row.id,
            dpiType: row.dpiType.id,
            areaRangeType: row.areaRangeType.id,
            price: row.price ?? '',
            cost: row.cost ?? '',
            printHours: row.printHours ?? '',
          },
        ]),
      ),
      pricingByPiece: Object.fromEntries(
        sortById(option.pricingByPiece ?? []).map((row) => [
          buildPieceKey(row.variantType.id),
          {
            id: row.id,
            variantType: row.variantType.id,
            price: row.price ?? '',
            cost: row.cost ?? '',
            printHours: row.printHours ?? '',
          },
        ]),
      ),
      pricingByCut: Object.fromEntries(
        sortById(option.pricingByCut ?? []).map((row) => [
          buildCutKey(row.type.id),
          {
            id: row.id,
            type: row.type.id,
            price: row.price ?? '',
          },
        ]),
      ),
    })),
  };
}

function mapOptionToPayload(option: MaterialOptionFormValues) {
  const pricingByArea = Object.values(option.pricingByArea)
    .filter(hasAreaRowValues)
    .sort((a, b) => a.areaRangeType - b.areaRangeType || a.dpiType - b.dpiType)
    .map((row) => ({
      ...(row.id ? { id: row.id } : {}),
      dpiType: row.dpiType,
      areaRangeType: row.areaRangeType,
      price: row.price.trim(),
      cost: row.cost.trim(),
      printHours: row.printHours.trim(),
    }));

  const pricingByPiece = Object.values(option.pricingByPiece)
    .filter(hasPieceRowValues)
    .sort((a, b) => a.variantType - b.variantType)
    .map((row) => ({
      ...(row.id ? { id: row.id } : {}),
      variantType: row.variantType,
      price: row.price.trim(),
      cost: row.cost.trim(),
      printHours: row.printHours.trim(),
    }));

  const pricingByCut = option.isCut
    ? Object.values(option.pricingByCut)
        .filter(hasCutRowValues)
        .sort((a, b) => a.type - b.type)
        .map((row) => ({
          ...(row.id ? { id: row.id } : {}),
          type: row.type,
          price: row.price.trim(),
        }))
    : [];

  const processings = option.processings
    .filter((processing) => processing.processingId > 0)
    .sort((a, b) => a.processingId - b.processingId)
    .map((processing) => ({
      ...(processing.id ? { id: processing.id } : {}),
      processingId: processing.processingId,
    }));

  return {
    ...(option.id ? { id: option.id } : {}),
    name: option.name.trim(),
    pricingType: option.pricingTypeId,
    isCut: option.isCut,
    pricingByArea: option.pricingTypeId === 1 ? pricingByArea : [],
    pricingByPiece: option.pricingTypeId === 2 ? pricingByPiece : [],
    pricingByCut,
    processings,
  };
}

function buildMaterialBody(values: MaterialFormValues) {
  const description = values.description?.trim();
  const options = values.options.map(mapOptionToPayload);

  return {
    name: values.name.trim(),
    description: description || undefined,
    options,
  };
}

export function buildCreateMaterialBody(values: MaterialFormValues): CreateMaterialBody {
  return buildMaterialBody(values);
}

export function buildUpdateMaterialBody(values: MaterialFormValues): UpdateMaterialBody {
  return buildMaterialBody(values);
}
