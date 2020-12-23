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
    await getOnServer.getData('?ctrl=trabalhos')
}

async function readRepositorySite(){

    //await readResponse()
    await showTrbs()
    
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

async function showTrbs(){
    
    try {
        const trbs = await getOnServer.getData('')

        if(trbs.fail){
            render.tag(container, 'p', {text: trbs.fail})
            return
        }
        const trbsOrganized = organizeByYear(trbs)

        render.tag(container, 'h3', {
            text: `Foram localizados ${trbsOrganized.length} trabalhos no repositorio UFSC`
        })

        renderDetails(trbsOrganized)
    } catch (error) {
        console.error(error)
    }
     
}

function renderDetails(trbsOrganized){

    for(let year in trbsOrganized){
        const details = render.tag(container, 'details', {class: 'repository_site'})
        const summary = render.tag(details, 'summary', {
            text: `${year} (${trbsOrganized[year].length})`
        })

        renderTable(trbsOrganized[year], details)

    }  
}

function renderTable(trbs, container){

    const titles = ['Título', 'Autor', 'Ano']
        
    const table = render.tag(container, 'table')

    const tableRowTitles = render.tag(table, 'tr')
    titles.forEach(title=>{
        render.tag(tableRowTitles, 'th', {text: title})
    })

    trbs.forEach(trb => {
        let tableRow = render.tag(table, 'tr')

        const tableCellTitle = render.tag(tableRow, 'td')
        
        render.tag(tableCellTitle, 'a', {text: trb.title, href: trb.url, target: '_blank'})
        render.tag(tableRow, 'td', {text: trb.author})
        render.tag(tableRow, 'td', {text: trb.year})
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