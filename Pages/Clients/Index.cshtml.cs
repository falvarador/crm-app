using CrmApp.Data;
using CrmApp.Data.Models;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.EntityFrameworkCore;
using Task = System.Threading.Tasks.Task;

namespace CrmApp.Pages.Clients;

public class IndexModel(ApplicationDbContext context) : PageModel
{
    public IList<Client> Client { get;set; } = null!;

    public async Task OnGetAsync()
    {
        Client = await context.Client
            .Include(c => c.User).ToListAsync();
    }
}