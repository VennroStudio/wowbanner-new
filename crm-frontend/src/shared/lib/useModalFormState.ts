import { useState } from 'react';

interface UseModalFormStateOptions {
  onClose: () => void;
  onReset?: () => void;
}

export const useModalFormState = ({
  onClose,
  onReset,
}: UseModalFormStateOptions) => {
  const [submitError, setSubmitError] = useState<string | null>(null);

  const handleClose = () => {
    setSubmitError(null);
    onReset?.();
    onClose();
  };

  return {
    submitError,
    setSubmitError,
    handleClose,
  };
};
