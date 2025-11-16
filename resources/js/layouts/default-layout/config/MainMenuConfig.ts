import type { MenuItem } from "@/layouts/default-layout/config/types";

const MainMenuConfig: Array<MenuItem> = [
    {
        pages: [
            {
                heading: "Dashboard",
                name: "dashboard",
                route: "/dashboard",
                keenthemesIcon: "element-11",
            },
        ],
    },

    // WEBSITE
    {
        heading: "Website",
        route: "/dashboard/website",
        name: "website",
        pages: [
            // MASTER
            {
                sectionTitle: "Master",
                route: "/master",
                keenthemesIcon: "cube-3",
                name: "master",
                sub: [
                    {
                        sectionTitle: "User",
                        route: "/users",
                        name: "master-user",
                        sub: [
                            {
                                heading: "Role",
                                name: "master-role",
                                route: "/dashboard/master/users/roles",
                            },
                            {
                                heading: "User",
                                name: "master-user",
                                route: "/dashboard/master/users",
                            },
                        ],
                    },
                    {
                        sectionTitle: "Kabinet",
                        route: "/kabinet",
                        name: "master-kabinet",
                        sub: [
                            {
                                heading: "Dokumen",
                                name: "master-dokumen",
                                route: "/dashboard/master/dokumen",
                            },
                        ],
                    },
                ], 
            },

            {
                sectionTitle: "Sekolah",
                route: "/dashboard/sekolah",
                keenthemesIcon: "element-11",
                name: "sekolah",
                sub: [
                    {
                        heading: "Siswa",
                        name: "sekolah-siswa",
                        route: "/dashboard/sekolah/siswa",
                    },
                    {
                        heading: "Kelas",
                        name: "sekolah-siswa",
                        route: "/dashboard/sekolah/kelas",
                    },
                ],
            },
        
            {
                heading: "Setting",
                route: "/dashboard/setting",
                name: "setting",
                keenthemesIcon: "setting-2",
            },
        ],
    },
];

export default MainMenuConfig;
