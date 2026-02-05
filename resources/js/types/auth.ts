export type User = {
    id: number;
    uuid: string;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    roles?: { name: string }[];
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
    can?: {
        viewSystemDashboard?: boolean;
    };
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
