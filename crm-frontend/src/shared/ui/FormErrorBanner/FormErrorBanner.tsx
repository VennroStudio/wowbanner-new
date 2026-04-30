interface FormErrorBannerProps {
  message?: string | null;
}

export const FormErrorBanner = ({ message }: FormErrorBannerProps) => {
  if (!message) return null;

  return (
    <div className="rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-3 py-2">
      {message}
    </div>
  );
};
