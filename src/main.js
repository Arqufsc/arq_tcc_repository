import render from "./client/render.js";
import readTrabalhos from "./client/listTrabalhos.js";
import findLinks from "./client/findLinks.js";

const container = document.querySelector('main')
const restartButton = document.querySelector("#readRepository")

const readState = {
    page: 0,
    morePages: true,
    response: []
}

window.addEventListener('load', async ()=>{
    
    const trabalhos = await readTrabalhos.listTrabalhos('?ctrl=trabalhos')
    if(trabalhos.fail){
        render.tag(container, 'p', {text: trabalhos.fail})
    }else{
        render.tag(container, 'h3', {
            text: `Foram localizados ${readTrabalhos.countTrabalhos(trabalhos)} trabalhos registrados no site`
        })
        
        renderDetails(trabalhos)
    }

    await findLinks()

    restartButton.addEventListener('click', readRepositorySite)
})

async function readRepositorySite(){
    
    clearPage()

    render.tag(container, 'blink', {
        id: 'loading',
        text: "Lendo site do repositório institucional..."
    })
    await readTrabalhos.readRepositoryPage(readState)   
    
    if(readState.morePages === false){
        await getTrabalhosOnRepositoryPagesReaded()
    }    
}

function clearPage(){
    const title = container.querySelector('h2')
    const navigation = container.querySelector('nav')

    container.innerHTML = ""
    container.appendChild(title)
    container.appendChild(navigation)
}



/*async function showTrbs(url = ''){
    
    try {
        const trbs = await getOnServer.getData(url)

        if(trbs.fail){
            render.tag(container, 'p', {text: trbs.fail})
            return false
        }

        render.tag(container, 'h3', {
            text: `Foram localizados ${countTrbs(trbs)} trabalhos registrados no site`
        })

        renderDetails(trbs)

        return trbs

    } catch (error) {
        console.error(error)
    }
     
}

function countTrbs(trbs){
    let count = 0

    for(let semestre in trbs){
        count += trbs[semestre].length
    }

    return count
}*/

function renderDetails(trbsOrganized){

    for(let year in trbsOrganized){
        const details = render.tag(container, 'details', {
            class: 'repository_list',
            id: year,
            open: 'true'
        })
        const summary = render.tag(details, 'summary', {text: year})
        render.tag(summary, 'small', {text: trbsOrganized[year].length})

        renderTable(trbsOrganized[year], details)

    }  
}

function renderTable(trbs, container){

    const titles = ['Título', 'Autor', 'Link']
        
    const table = render.tag(container, 'table')

    const tableRowTitles = render.tag(table, 'tr')
    titles.forEach(title=>{
        render.tag(tableRowTitles, 'th', {text: title})
    })

    trbs.forEach(trb => {
        let tableRow = render.tag(table, 'tr', {id: trb.id})
        
        render.tag(tableRow, 'td', {text: trb.titulo, class: "table_title"})
        render.tag(tableRow, 'td', {text: trb.autor, class: "table_author"})

        if(trb.repository !== null){
            const linkColumn = render.tag(tableRow, 'td', {class: "table_repository"})
            render.tag(linkColumn, 'a', {
                text: "repositório",
                target: "_blank",
                href: trb.repository,
                class: "button"
            })
        }else{
            const linkColumn = render.tag(tableRow, 'td', {class: "table_repository empty"})
            render.tag(linkColumn, 'span', {text: 'Buscando...'})
        }
    });
}