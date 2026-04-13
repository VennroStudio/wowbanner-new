interface ClientsNoticeBannerProps {
  message: string;
}

export const ClientsNoticeBanner = ({ message }: ClientsNoticeBannerProps) => (
  <div className="mb-4 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-800 text-sm px-4 py-2">
    {message}
  </div>
);
