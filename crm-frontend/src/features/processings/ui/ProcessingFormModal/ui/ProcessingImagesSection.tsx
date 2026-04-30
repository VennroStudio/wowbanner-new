import { useRef, useState, type Dispatch, type SetStateAction } from 'react';
import { ImagePlus, Trash2 } from 'lucide-react';
import type { ProcessingImage } from '@/entities/processing';
import { useUploadProcessingImagesCommand, useDeleteProcessingImageCommand } from '@/entities/processing';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ImageAltTextInput } from './ImageAltTextInput';

interface Props {
  processingId: number;
  images: ProcessingImage[];
  altDrafts: Record<number, string>;
  setAltDrafts: Dispatch<SetStateAction<Record<number, string>>>;
}

export const ProcessingImagesSection = ({
  processingId,
  images,
  altDrafts,
  setAltDrafts,
}: Props) => {
  const fileInputRef = useRef<HTMLInputElement>(null);
  const uploadMutation = useUploadProcessingImagesCommand();
  const deleteMutation = useDeleteProcessingImageCommand();
  const [error, setError] = useState<string | null>(null);

  const getAlt = (img: ProcessingImage) =>
    altDrafts[img.id] !== undefined ? altDrafts[img.id] : (img.alt ?? '');

  const handleFiles = async (files: FileList | null) => {
    if (!files?.length) return;
    const list = Array.from(files);
    setError(null);
    try {
      await uploadMutation.mutateAsync({
        processingId,
        files: list,
        imageAlts: list.map(() => ''),
      });
      if (fileInputRef.current) fileInputRef.current.value = '';
    } catch (e) {
      setError(getApiErrorMessage(e));
    }
  };

  const removeImage = async (imageId: number) => {
    setError(null);
    try {
      await deleteMutation.mutateAsync({ imageId, processingId });
    } catch (e) {
      setError(getApiErrorMessage(e));
    }
  };

  return (
    <div className="border-t border-slate-100 pt-4 mt-2">
      <div className="flex items-center justify-between mb-3">
        <span className="text-xs font-semibold text-slate-500 uppercase tracking-wide">
          Изображения
        </span>
        <div>
          <input
            ref={fileInputRef}
            type="file"
            accept="image/*"
            multiple
            className="hidden"
            onChange={(e) => void handleFiles(e.target.files)}
          />
          <button
            type="button"
            onClick={() => fileInputRef.current?.click()}
            disabled={uploadMutation.isPending}
            className="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 disabled:opacity-50"
          >
            <ImagePlus size={14} />
            {uploadMutation.isPending ? 'Загрузка…' : 'Добавить файлы'}
          </button>
        </div>
      </div>

      <p className="text-[11px] text-slate-400 mb-2">
        Подписи к изображениям сохраняются вместе с кнопкой «Сохранить» внизу формы.
      </p>

      {error && (
        <div className="rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-3 py-2 mb-3">
          {error}
        </div>
      )}

      {images.length === 0 ? (
        <p className="text-xs text-slate-400">Пока нет изображений — загрузите файлы выше.</p>
      ) : (
        <ul className="space-y-3">
          {images.map((img) => (
            <li
              key={img.id}
              className="flex flex-col sm:flex-row gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/50"
            >
              <a
                href={img.path}
                target="_blank"
                rel="noopener noreferrer"
                className="shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-slate-200 border border-slate-200"
              >
                <img src={img.path} alt="" className="w-full h-full object-cover" />
              </a>
              <div className="flex-1 min-w-0 space-y-2">
                <label className="block text-[11px] font-medium text-slate-500">Подпись (alt)</label>
                <ImageAltTextInput
                  value={getAlt(img)}
                  onValueChange={(v) => setAltDrafts((d) => ({ ...d, [img.id]: v }))}
                />
              </div>
              <button
                type="button"
                onClick={() => void removeImage(img.id)}
                disabled={deleteMutation.isPending}
                className="self-start p-2 text-slate-400 hover:text-red-600 rounded-lg shrink-0"
                aria-label="Удалить изображение"
              >
                <Trash2 size={16} />
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};
