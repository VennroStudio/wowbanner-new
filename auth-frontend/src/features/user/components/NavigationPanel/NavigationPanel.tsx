import React from 'react';
import { LayoutDashboard } from 'lucide-react';
import { PanelActionButton } from '../PanelActionButton';
import { CRM_URL } from '@/shared/constants';

export const NavigationPanel: React.FC = () => {

  return (
    <div className="mb-8 text-left">
      <h3 className="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-1">
        Навигация
      </h3>
      <div className="grid grid-cols-2 gap-3">
          {/*icon={Globe}*/}
          <PanelActionButton
              label="WoWBanner"
              disabled
          />

        <PanelActionButton
          icon={LayoutDashboard}
          label="CRM"
          onClick={() => { window.location.href = CRM_URL; }}
        />
      </div>
    </div>
  );
};
