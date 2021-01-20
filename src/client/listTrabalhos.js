import getOnServer from "./getOnServer.js";

const readTrabalhos = {
    listTrabalhos: async ()=>{
        
        try {
            return await getOnServer.getData('?ctrl=trabalhos')

        } catch (error) {
            console.error(error)
        }     
    },

    countTrabalhos: trabalhos=>{
        let count = 0

        for(let semestre in trabalhos){
            count += trabalhos[semestre].length
        }

        return count
    },

    readRepositoryPage: async (readState, loandingMsg)=>{
        try{
            while(readState.morePages){
                let response = await getOnServer.getData(`?ctrl=repositorio&act=restart&page=${readState.page}`)

                readState.page++
                readState.morePages = response.morePages
                loandingMsg.innerText = `Lendo página ${readState.page}...`
            }
            

        } catch (error) {
            console.error(error)
        }

        try {
            const cleanTrabalhosList = await getOnServer.getData("?ctrl=repositorio&act=trabalhos")
            loandingMsg.innerText = `Filtrando dados...`

        } catch (error) {
            console.error(error)
        }

        return true
    }
}

export default readTrabalhos