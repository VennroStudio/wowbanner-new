import type { ProcessingImage } from '@/entities/processing';

export function getDirtyProcessingImageAltUpdates(
  images: ProcessingImage[],
  altDrafts: Record<number, string>,
): { imageId: number; alt: string }[] {
  const out: { imageId: number; alt: string }[] = [];
  for (const img of images) {
    const server = (img.alt ?? '').trim();
    const draft = altDrafts[img.id] !== undefined ? altDrafts[img.id] : (img.alt ?? '');
    const current = draft.trim();
    if (current !== server) {
      out.push({ imageId: img.id, alt: current });
    }
  }
  return out;
}
