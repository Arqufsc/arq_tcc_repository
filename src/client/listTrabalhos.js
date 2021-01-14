import getOnServer from "./getOnServer.js";

const readTrabalhos = {
    listTrabalhos: async url=>{
        
        try {
            return await getOnServer.getData(url)

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

    readRepositoryPage: async readState=>{
        try{
            await getOnServer.getData(`?ctrl=repositorio&act=restart&page=${readState.page}`)
            readState.page++
            readState.morePages = readState.response.morePages

            return readState
        } catch (error) {
            console.error(error)
        }
    }
}

export default readTrabalhos