import { useEffect, useState, type ReactNode } from 'react';
import { Extension } from '@tiptap/core';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import TextStyle from '@tiptap/extension-text-style';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import Table from '@tiptap/extension-table';
import TableRow from '@tiptap/extension-table-row';
import TableCell from '@tiptap/extension-table-cell';
import TableHeader from '@tiptap/extension-table-header';
import {
  Bold,
  Italic,
  Heading1,
  Heading2,
  Heading3,
  List,
  ListOrdered,
  Quote,
  Link2,
  Table2,
  Trash2,
  Undo2,
  Redo2,
  Palette,
} from 'lucide-react';
import './richTextEditor.css';

const TEXT_COLORS = [
  '#0f172a',
  '#475569',
  '#2563eb',
  '#7c3aed',
  '#db2777',
  '#dc2626',
  '#ea580c',
  '#ca8a04',
  '#16a34a',
  '#0891b2',
];

const TextColor = Extension.create({
  name: 'textColor',

  addOptions() {
    return {
      types: ['textStyle'],
    };
  },

  addGlobalAttributes() {
    return [
      {
        types: this.options.types,
        attributes: {
          color: {
            default: null,
            parseHTML: (element) => element.style.color || null,
            renderHTML: (attributes) => {
              if (!attributes.color) {
                return {};
              }

              return {
                style: `color: ${attributes.color}`,
              };
            },
          },
        },
      },
    ];
  },
});

export interface RichTextEditorProps {
  value: string;
  onChange: (html: string) => void;
  placeholder?: string;
  disabled?: boolean;
  className?: string;
}

function ToolbarButton({
  active,
  disabled,
  onClick,
  title,
  children,
}: {
  active?: boolean;
  disabled?: boolean;
  onClick: () => void;
  title: string;
  children: ReactNode;
}) {
  return (
    <button
      type="button"
      title={title}
      disabled={disabled}
      onClick={onClick}
      className={[
        'inline-flex items-center justify-center w-8 h-8 rounded-md border text-slate-600 transition-colors',
        active
          ? 'bg-blue-50 border-blue-200 text-blue-700'
          : 'bg-white border-slate-200 hover:bg-slate-50 hover:border-slate-300',
        disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
      ].join(' ')}
    >
      {children}
    </button>
  );
}

export function RichTextEditor({
  value,
  onChange,
  placeholder = '',
  disabled = false,
  className = '',
}: RichTextEditorProps) {
  const [showPalette, setShowPalette] = useState(false);
  const editor = useEditor({
    immediatelyRender: true,
    extensions: [
      StarterKit.configure({
        heading: { levels: [1, 2, 3] },
      }),
      TextStyle,
      TextColor,
      Link.configure({
        openOnClick: false,
        HTMLAttributes: { class: 'text-blue-600 underline' },
      }),
      Placeholder.configure({
        placeholder: placeholder || ' ',
        emptyEditorClass: 'is-editor-empty',
      }),
      Table.configure({
        resizable: false,
        HTMLAttributes: { class: 'rich-text-table' },
      }),
      TableRow,
      TableHeader,
      TableCell,
    ],
    content: value || '',
    editable: !disabled,
    editorProps: {
      attributes: {
        class: 'prose prose-sm max-w-none focus:outline-none',
      },
    },
    onUpdate: ({ editor: ed }) => {
      onChange(ed.getHTML());
    },
  });

  useEffect(() => {
    if (!editor) return;
    const current = editor.getHTML();
    if (value !== current) {
      editor.commands.setContent(value || '', false);
    }
  }, [editor, value]);

  useEffect(() => {
    if (!editor) return;
    editor.setEditable(!disabled);
  }, [editor, disabled]);

  if (!editor) {
    return (
      <div
        className={[
          'rich-text-editor rounded-lg border border-slate-200 bg-slate-50 min-h-[10rem]',
          disabled ? 'rich-text-editor--disabled' : '',
          className,
        ].join(' ')}
      />
    );
  }

  const setLink = () => {
    const previous = editor.getAttributes('link').href;
    const next = window.prompt('URL ссылки', previous ?? 'https://');
    if (next === null) return;
    if (next === '') {
      editor.chain().focus().extendMarkRange('link').unsetLink().run();
      return;
    }
    editor.chain().focus().extendMarkRange('link').setLink({ href: next }).run();
  };

  const currentColor = String(editor.getAttributes('textStyle').color || '').trim();

  const applyTextColor = (color: string) => {
    editor.chain().focus().setMark('textStyle', { color }).run();
    setShowPalette(false);
  };

  const clearTextColor = () => {
    editor.chain().focus().setMark('textStyle', { color: null }).removeEmptyTextStyle().run();
    setShowPalette(false);
  };

  return (
    <div
      className={[
        'rich-text-editor rounded-lg border border-slate-200 overflow-hidden bg-white',
        disabled ? 'rich-text-editor--disabled' : '',
        className,
      ].join(' ')}
    >
      <div className="flex flex-wrap gap-1 px-2 py-1.5 border-b border-slate-200 bg-slate-50/80">
        <ToolbarButton
          title="Жирный"
          disabled={disabled}
          active={editor.isActive('bold')}
          onClick={() => editor.chain().focus().toggleBold().run()}
        >
          <Bold size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Курсив"
          disabled={disabled}
          active={editor.isActive('italic')}
          onClick={() => editor.chain().focus().toggleItalic().run()}
        >
          <Italic size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Заголовок 1"
          disabled={disabled}
          active={editor.isActive('heading', { level: 1 })}
          onClick={() => editor.chain().focus().toggleHeading({ level: 1 }).run()}
        >
          <Heading1 size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Заголовок 2"
          disabled={disabled}
          active={editor.isActive('heading', { level: 2 })}
          onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()}
        >
          <Heading2 size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Заголовок 3"
          disabled={disabled}
          active={editor.isActive('heading', { level: 3 })}
          onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()}
        >
          <Heading3 size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Маркированный список"
          disabled={disabled}
          active={editor.isActive('bulletList')}
          onClick={() => editor.chain().focus().toggleBulletList().run()}
        >
          <List size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Нумерованный список"
          disabled={disabled}
          active={editor.isActive('orderedList')}
          onClick={() => editor.chain().focus().toggleOrderedList().run()}
        >
          <ListOrdered size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Цитата"
          disabled={disabled}
          active={editor.isActive('blockquote')}
          onClick={() => editor.chain().focus().toggleBlockquote().run()}
        >
          <Quote size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Цвет текста"
          disabled={disabled}
          active={showPalette || Boolean(currentColor)}
          onClick={() => setShowPalette((value) => !value)}
        >
          <Palette size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Ссылка"
          disabled={disabled}
          active={editor.isActive('link')}
          onClick={setLink}
        >
          <Link2 size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Вставить таблицу 3×3"
          disabled={disabled}
          active={editor.isActive('table')}
          onClick={() =>
            editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()
          }
        >
          <Table2 size={14} />
        </ToolbarButton>
        {editor.can().deleteTable() && (
          <ToolbarButton
            title="Удалить таблицу"
            disabled={disabled}
            onClick={() => editor.chain().focus().deleteTable().run()}
          >
            <Trash2 size={14} />
          </ToolbarButton>
        )}
        <ToolbarButton
          title="Отменить"
          disabled={disabled || !editor.can().undo()}
          onClick={() => editor.chain().focus().undo().run()}
        >
          <Undo2 size={14} />
        </ToolbarButton>
        <ToolbarButton
          title="Повторить"
          disabled={disabled || !editor.can().redo()}
          onClick={() => editor.chain().focus().redo().run()}
        >
          <Redo2 size={14} />
        </ToolbarButton>
      </div>
      {showPalette ? (
        <div className="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-white px-3 py-2">
          {TEXT_COLORS.map((color) => {
            const active = currentColor.toLowerCase() === color.toLowerCase();

            return (
              <button
                key={color}
                type="button"
                title={color}
                disabled={disabled}
                onClick={() => applyTextColor(color)}
                className={[
                  'h-7 w-7 rounded-full border-2 transition-transform',
                  active ? 'scale-110 border-slate-900' : 'border-white hover:scale-105',
                  disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
                ].join(' ')}
                style={{ backgroundColor: color }}
              />
            );
          })}
          <button
            type="button"
            disabled={disabled}
            onClick={clearTextColor}
            className="ml-1 rounded-md border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
          >
            Сбросить
          </button>
        </div>
      ) : null}
      <EditorContent editor={editor} />
    </div>
  );
}
