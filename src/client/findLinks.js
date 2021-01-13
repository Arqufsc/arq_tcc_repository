import getOnServer from "./getOnServer.js";
import render from "./render.js"

function changeTableTitle(detail, total){
    const summarySmall = detail.querySelector("summary small")
    const tableRows = detail.querySelectorAll('table tr')

    let count

    tableRows.forEach(row=>{
        const cellLink = row.querySelector('.empty')

        if(cellLink){
            count --
        }
    })

    const text = `${count} links num total de ${total} trabalhos`
    summarySmall.innerText = text
}

function setStatistic(){

}

async function searchLinks(row){
    const cellTitle = row.querySelector('.table_title')
    const cellLink = row.querySelector('.empty')

    console.log(`?ctrl=trabalhos&act=find&id=${row.id}`)
    const search = await getOnServer.getData(`?ctrl=trabalhos&act=find&id=${row.id}`)

    if(search.error){
        return false
    }
    cellLink.innerHTML = ""
    
    if(search.trb){
        cellTitle.innerText = search.trb.title

        render.tag(cellLink, 'a', {
            text: 'repositório',
            target: '_blank',
            href: search.trb.url,
            class: 'button'
        })

        cellLink.setAttribute('class', 'table_repository')
    }

}

async function findLinks(){
    const container = document.querySelector('main')
    const details = document.querySelectorAll("details")

    const estatistica = {
        total: 0,
        fail: 0,
        multiplos: [],
        fails: [],
        success: 0
    }

    details.forEach(async detail=>{
        const tableRows = detail.querySelectorAll("table tr")
        const total = tableRows.length - 1
    
        changeTableTitle(detail, total)
    
        tableRows.forEach(async row=>{
            
             await searchLinks(row)
            //setStatistic()
        })
    })
}

export default findLinks