import { useRef, useState } from 'react';
import { ImagePlus, Trash2 } from 'lucide-react';
import type { MaterialImage } from '@/entities/material';
import {
  useUploadMaterialImagesCommand,
  useUpdateMaterialImageAltCommand,
  useDeleteMaterialImageCommand,
} from '@/entities/material';
import { fieldInputClass } from '@/shared/ui';
import { getApiErrorMessage } from '@/shared/utils/axiosError';

interface Props {
  materialId: number;
  images: MaterialImage[];
}

export const MaterialImagesSection = ({ materialId, images }: Props) => {
  const fileInputRef = useRef<HTMLInputElement>(null);
  const uploadMutation = useUploadMaterialImagesCommand();
  const updateAltMutation = useUpdateMaterialImageAltCommand();
  const deleteMutation = useDeleteMaterialImageCommand();
  const [error, setError] = useState<string | null>(null);
  const [altDrafts, setAltDrafts] = useState<Record<number, string>>({});

  const getAlt = (img: MaterialImage) =>
    altDrafts[img.id] !== undefined ? altDrafts[img.id] : (img.alt ?? '');

  const handleFiles = async (files: FileList | null) => {
    if (!files?.length) return;
    const list = Array.from(files);
    setError(null);
    try {
      await uploadMutation.mutateAsync({
        materialId,
        files: list,
        imageAlts: list.map(() => ''),
      });
      if (fileInputRef.current) fileInputRef.current.value = '';
    } catch (e) {
      setError(getApiErrorMessage(e));
    }
  };

  const saveAlt = async (imageId: number) => {
    const img = images.find((i) => i.id === imageId);
    const alt =
      altDrafts[imageId] !== undefined ? altDrafts[imageId] : (img?.alt ?? '');
    setError(null);
    try {
      await updateAltMutation.mutateAsync({
        imageId,
        alt,
        materialId,
      });
    } catch (e) {
      setError(getApiErrorMessage(e));
    }
  };

  const removeImage = async (imageId: number) => {
    setError(null);
    try {
      await deleteMutation.mutateAsync({ imageId, materialId });
    } catch (e) {
      setError(getApiErrorMessage(e));
    }
  };

  return (
    <div className="border-t border-slate-100 pt-4 mt-2">
      <div className="flex items-center justify-between mb-3">
        <span className="text-xs font-semibold text-slate-500 uppercase tracking-wide">Изображения</span>
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
                <div className="flex flex-wrap items-center gap-2">
                  <input
                    type="text"
                    value={getAlt(img)}
                    onChange={(e) => setAltDrafts((d) => ({ ...d, [img.id]: e.target.value }))}
                    className={`${fieldInputClass} flex-1 min-w-[120px] text-sm py-1.5`}
                    placeholder="Описание для доступности"
                  />
                  <button
                    type="button"
                    onClick={() => void saveAlt(img.id)}
                    disabled={updateAltMutation.isPending}
                    className="px-2.5 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded-lg border border-blue-100"
                  >
                    Сохранить подпись
                  </button>
                </div>
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
