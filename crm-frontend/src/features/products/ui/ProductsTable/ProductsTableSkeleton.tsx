export const ProductsTableSkeleton = () => (
  <>
    {Array.from({ length: 6 }).map((_, index) => (
      <tr key={index} className="border-b border-slate-100 last:border-0">
        <td className="px-5 py-4"><div className="h-4 w-12 rounded bg-slate-100 animate-pulse" /></td>
        <td className="px-5 py-4"><div className="h-4 w-40 rounded bg-slate-100 animate-pulse" /></td>
        <td className="px-5 py-4"><div className="h-6 w-48 rounded bg-slate-100 animate-pulse" /></td>
        <td className="px-5 py-4"><div className="h-6 w-36 rounded bg-slate-100 animate-pulse" /></td>
        <td className="px-5 py-4"><div className="h-8 w-28 rounded bg-slate-100 animate-pulse" /></td>
      </tr>
    ))}
  </>
);
