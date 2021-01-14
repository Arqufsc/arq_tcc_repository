import getOnServer from "./getOnServer.js";
import render from "./render.js"

function changeTableTitle(detail, total){
    const summarySmall = detail.querySelector("summary small")
    const tableRows = detail.querySelectorAll('table tr')

    let count = total

    tableRows.forEach(row=>{
        const cellLink = row.querySelector('.empty')

        if(cellLink){
            count--
        }
    })

    const text = `${count} links num total de ${total} trabalhos`
    summarySmall.innerText = text
}

function setStatistic(total, searches){

    const estatistica = {
        total: total,
        fail: 0,
        multiplos: [],
        fails: [],
        success: total
    }
    console.log(searches)
    searches.forEach(search=>{
        if(search.fail){
            estatistica.fail++
            estatistica.success--
            fails.push(search.fail)
        }

        if(search.multiplos){
            estatistica.fail++
            estatistica.success--
            estatistica.multiplos.push(search.searchResult)
        }
    })

    console.log(estatistica)
}

async function searchLinks(row, cellLink){
    const cellTitle = row.querySelector('.table_title')
    
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

    if(search.fail){
        render.tag(cellLink, 'span', {
            text: "Nenhum link",
            class: "not_found"
        })
    }

    if(search.multiplos){
        render.tag(cellLink, 'span', {
            text: "Múltiplos links",
            class: "multiplos"
        })
    }

    return search

}

async function findLinks(){
    const details = document.querySelectorAll("details")
    const searches = []
    let total = 0

    details.forEach(async detail=>{
        const tableRows = detail.querySelectorAll("table tr")
        const count = tableRows.length - 1

        total = total + count
    
        changeTableTitle(detail, count)
    
        tableRows.forEach(async row=>{            
            const cellLink = row.querySelector('.empty')

            if(cellLink){
                const searchResult = await searchLinks(row, cellLink)
                searches.push(searchResult)
            }

        })
    })
    
    setStatistic(total, searches)
}

export default findLinks