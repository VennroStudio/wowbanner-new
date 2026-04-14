export const MaterialsTableSkeleton = () => (
  <>
    {[...Array(8)].map((_, i) => (
      <tr key={i} className="animate-pulse border-b border-slate-100 last:border-0">
        <td className="px-5 py-4">
          <div className="h-3.5 w-14 bg-slate-100 rounded" />
        </td>
        <td className="px-5 py-4">
          <div className="flex flex-col gap-2">
            <div className="h-4 w-48 bg-slate-200 rounded" />
            <div className="h-3 w-full max-w-xs bg-slate-100 rounded" />
          </div>
        </td>
        <td className="px-5 py-4">
          <div className="flex gap-1.5">
            <div className="h-10 w-10 bg-slate-100 rounded-md" />
            <div className="h-10 w-10 bg-slate-100 rounded-md" />
          </div>
        </td>
        <td className="px-5 py-4">
          <div className="h-4 w-12 bg-slate-100 rounded" />
        </td>
        <td className="px-5 py-4">
          <div className="flex items-center gap-2">
            <div className="h-7 w-28 bg-slate-100 rounded-lg" />
            <div className="h-7 w-7 bg-slate-100 rounded-lg" />
          </div>
        </td>
      </tr>
    ))}
  </>
);
