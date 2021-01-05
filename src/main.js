import getOnServer from "./client/getOnServer.js";
import render from "./client/render.js"

const container = document.querySelector('main')
const readState = {
    page: 0,
    morePages: true,
    response: []
}

window.addEventListener('load', async ()=>{

    //await readRepositorySite()
    await readTccSite()

})

async function readTccSite(){
    const trbs = await showTrbs('?ctrl=trabalhos')
    await findLinks()
}

async function findLinks(){
    const details = document.querySelectorAll("details")

    details.forEach(detail=>{
        let summarySmall = detail.querySelector("summary small")

        let tableRows = detail.querySelectorAll('table tr')
        
        const total = tableRows.length - 1
        let count = total
        tableRows.forEach(async row=>{
            if(row.querySelector('.empty')){
                count--
                await getOnServer.getData(`?ctrl=trabalhos&act=find&id=${row.id}`)
            }
        })

        summarySmall.innerText = `${count} links num total de ${total} trabalhos` 
    })
}

async function readRepositorySite(){

    await readResponse()
    //await showTrbs()
    
}

async function readResponse(){    
    while (readState.morePages) {
        try {
            readState.response = await getOnServer.getData(`?page=${readState.page}`)
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
                text: "repositorio",
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

function organizeByYear(trbs){
    const response = {}
    const years = []
    let yearList

    trbs.forEach(trb=>{
        let yearStrict = trb.year.replace('[', '').substr(0, 4)
        if(years.indexOf(yearStrict) == -1){
            years.push(yearStrict)
        }
    })

    years.forEach(year=>{
        yearList = []
        trbs.forEach(trb=>{
            let yearStrict = trb.year.substr(0, 4)
            if(yearStrict == year){
                yearList.push(trb)
            }
        })
        yearList.sort((a, b)=>a.title-b.title)
        response[year] = yearList
    })

    return response
}