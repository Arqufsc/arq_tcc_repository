import getOnServer from "./client/getOnServer.js";
import render from "./client/render.js"

const container = document.querySelector('main')
const restartButton = document.querySelector("#readRepository")

const readState = {
    page: 0,
    morePages: true,
    response: []
}

window.addEventListener('load', async ()=>{
    
    await readTccSite()
    
    restartButton.addEventListener('click', readRepositorySite)
})

async function readTccSite(){
    const trbs = await showTrbs('?ctrl=trabalhos')
    const searchDone = await findLinks()
    /*
    if(searchDone === false){
        readRepositorySite()
    }*/
}

async function findLinks(){
    const details = document.querySelectorAll("details")
    const estatistica = {
        total: 0,
        fail: 0,
        fails: [],
        success: 0
    }

    details.forEach(detail=>{
        let summarySmall = detail.querySelector("summary small")
        let summary = detail.querySelector("summary")

        let tableRows = detail.querySelectorAll('table tr')
        
        const total = tableRows.length - 1
        let count = total

        tableRows.forEach(async row=>{
            const cellTitle = row.querySelector('.table_title')
            const cellLink = row.querySelector('.empty')

            if(cellLink){
                count--
                const search = await getOnServer.getData(`?ctrl=trabalhos&act=find&id=${row.id}`)

                if(search.error){
                    return false
                }

                estatistica.total++
                cellLink.innerHTML = ""

                if(search.trb){
                    estatistica.success++

                    cellTitle.innerText = search.trb.title

                    render.tag(cellLink, 'a', {
                        text: 'repositório',
                        target: '_blank',
                        href: search.trb.url,
                        class: 'button'
                    })

                    cellLink.setAttribute('class', 'table_repository')
                }
                if(search.fail){
                    estatistica.fail++
                    estatistica.fails.push(`${summary.innerText.substr(0, 6)} - ${row.id}`)
                    console.log(`${row.id}: ${search.fail}`)

                    const falseButton = render.tag(cellLink, 'button', {
                        text: "não encontrado!",
                        class: 'not_found',
                        id: row.id
                    })
                }
                if(search.multiplos){
                    estatistica.fail++
                    estatistica.fails.push(`${summary.innerText.substr(0, 6)} - ${row.id}`)
                    
                    const falseButton = render.tag(cellLink, 'button', {
                        text: "!!! Múltplos !!!",
                        class: 'multiplos',
                        id: row.id
                    })

                }
            }
        })

        summarySmall.innerText = `${count} links num total de ${total} trabalhos` 
    })

    console.log(estatistica)
    return true
}

async function readRepositorySite(){
    
    clearPage()
    render.tag(container, 'blink', {
        id: 'loading',
        text: "Lendo site do repositório institucional..."
    })
    await readResponse()   
    
    if(readState.morePages === false){
        await getTrabalhosOnRepositoryPagesReaded()
    }    
}

async function getTrabalhosOnRepositoryPagesReaded(){
    await getOnServer.getData(`?ctrl=repositorio&act=trabalhos`)

    clearPage()

    await readTccSite()
}

function clearPage(){
    const title = container.querySelector('h2')
    const navigation = container.querySelector('nav')

    container.innerHTML = ""
    container.appendChild(title)
    container.appendChild(navigation)
}


async function readResponse(){    
    while (readState.morePages) {
        try {
            readState.response = await getOnServer.getData(`?ctrl=repositorio&act=restart&page=${readState.page}`)
            render.readingPage(container, readState.page)
            readState.page++
            readState.morePages = readState.response.morePages
        } catch (error) {
            console.error(error)
        }
    } ;
    
    render.endToRead(container)
}

async function showTrbs(url = ''){
    
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
}

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