using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;

namespace CrmApp.Areas.Identity.Data;

public class IdentityDataContext(DbContextOptions<IdentityDataContext> options)
    : IdentityDbContext<IdentityUser>(options);
