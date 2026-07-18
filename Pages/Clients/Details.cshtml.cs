using CrmApp.Data;
using CrmApp.Data.Models;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.EntityFrameworkCore;

namespace CrmApp.Pages.Clients;

public class DetailsModel(ApplicationDbContext context) : PageModel
{
    public Client Client { get; set; } = null!;

    public async Task<IActionResult> OnGetAsync(int? id)
    {
        if (id == null)
        {
            return NotFound();
        }

        var client = await context.Client.FirstOrDefaultAsync(m => m.Id == id);

        if (client is null) return NotFound();
        Client = client;

        return Page();
    }
}