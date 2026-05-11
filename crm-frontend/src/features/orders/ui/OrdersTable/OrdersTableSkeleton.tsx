export const OrdersTableSkeleton = () => (
  <>
    {Array.from({ length: 6 }).map((_, index) => (
      <tr key={index} className="border-b border-amber-100 last:border-0 bg-amber-50/40">
        {Array.from({ length: 14 }).map((_, cellIndex) => (
          <td key={cellIndex} className="px-4 py-4">
            <div className="h-8 w-24 rounded bg-slate-100 animate-pulse" />
          </td>
        ))}
      </tr>
    ))}
  </>
);
