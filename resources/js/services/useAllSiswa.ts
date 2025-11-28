import { useQuery } from "@tanstack/vue-query";
import axios from "@/libs/axios";

export function useAllSiswa(options = {}) {
    return useQuery({
        queryKey: ["siswas"],
        queryFn: async () =>
            await axios.get(`/siswas`).then((res: any) => res.data.data),
        ...options,
    })
}
